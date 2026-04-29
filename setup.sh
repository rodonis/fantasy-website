#!/bin/sh
# Fantasy Wiki — Alpine Linux setup script
# Run as root from the repo directory.
set -e

WIKI_ROOT="$(cd "$(dirname "$0")" && pwd)"
WIKI_CONF=/etc/apache2/conf.d/wiki.conf

echo "==> Fantasy Wiki setup — root: $WIKI_ROOT"

# ── 1. Load .env ──────────────────────────────────────────────────────────────
if [ ! -f "$WIKI_ROOT/.env" ]; then
    echo "ERROR: .env not found. Copy .env.example to .env and fill in values first."
    exit 1
fi
# shellcheck disable=SC1090
set -a; . "$WIKI_ROOT/.env"; set +a

# ── 2. Install packages ───────────────────────────────────────────────────────
echo "==> Installing packages..."
apk add --no-cache \
    apache2 \
    php83 php83-apache2 php83-pdo php83-pdo_mysql \
    php83-gd php83-session php83-mbstring php83-fileinfo php83-json \
    mariadb mariadb-client

# ── 3. Enable Apache modules ──────────────────────────────────────────────────
echo "==> Enabling Apache modules..."
# Uncomment if commented out; no-op if already active
sed -i 's/^#\(LoadModule rewrite_module\)/\1/' /etc/apache2/httpd.conf || true
sed -i 's/^#\(LoadModule headers_module\)/\1/' /etc/apache2/httpd.conf || true

# Ensure conf.d/ is included (Alpine ships this by default but be safe)
grep -q 'Include /etc/apache2/conf.d' /etc/apache2/httpd.conf || \
    echo 'Include /etc/apache2/conf.d/*.conf' >> /etc/apache2/httpd.conf

# ── 4. Write Apache vhost ─────────────────────────────────────────────────────
echo "==> Installing Apache vhost..."
sed "s|WIKI_ROOT|$WIKI_ROOT|g" "$WIKI_ROOT/apache/wiki.conf" > "$WIKI_CONF"

# Disable default localhost vhost so ours takes precedence
sed -i 's|^DocumentRoot "/var/www/localhost/htdocs"|#DocumentRoot "/var/www/localhost/htdocs"|' \
    /etc/apache2/httpd.conf 2>/dev/null || true

# ── 5. Set file permissions ───────────────────────────────────────────────────
echo "==> Setting permissions..."
chown -R apache:apache "$WIKI_ROOT/src/public/uploads"
chmod -R 775 "$WIKI_ROOT/src/public/uploads"
# .env must not be web-readable (it's outside public/, so Apache can't serve it,
# but restrict anyway)
chown root:apache "$WIKI_ROOT/.env"
chmod 640 "$WIKI_ROOT/.env"

# ── 6. MariaDB setup ──────────────────────────────────────────────────────────
echo "==> Setting up MariaDB..."

if [ ! -d /var/lib/mysql/mysql ]; then
    mysql_install_db --user=mysql --datadir=/var/lib/mysql
fi

rc-service mariadb start 2>/dev/null || true
sleep 2

# Create database and user
mysql -u root <<SQL
CREATE DATABASE IF NOT EXISTS \`${WIKI_DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${WIKI_DB_USER}'@'localhost' IDENTIFIED BY '${WIKI_DB_PASS}';
GRANT ALL PRIVILEGES ON \`${WIKI_DB_NAME}\`.* TO '${WIKI_DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL

echo "==> Running migrations..."
mysql -u "${WIKI_DB_USER}" -p"${WIKI_DB_PASS}" "${WIKI_DB_NAME}" \
    < "$WIKI_ROOT/src/migrations/001_init.sql"

echo "==> Seeding example data..."
# Compute bcrypt hash for GM password using PHP
GM_HASH=$(php83 -r "echo password_hash('${WIKI_GM_PASS}', PASSWORD_DEFAULT);")

# Swap placeholder hash in seed file for the real one, run it
sed "s|\\\$2y\\\$12\\\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi|${GM_HASH}|g" \
    "$WIKI_ROOT/src/migrations/002_seed.sql" | \
    mysql -u "${WIKI_DB_USER}" -p"${WIKI_DB_PASS}" "${WIKI_DB_NAME}"

# ── 7. Enable services ────────────────────────────────────────────────────────
echo "==> Enabling services..."
rc-update add mariadb default 2>/dev/null || true
rc-update add apache2 default 2>/dev/null || true

rc-service apache2 restart

echo ""
echo "==> Done."
echo ""
echo "    Wiki running on http://localhost/"
echo "    GM login: ${WIKI_GM_USER} / ${WIKI_GM_PASS}"
echo "    Change the GM password after first login."
echo ""
echo "    Point your nginxpm proxy to this VM's IP on port 80."
