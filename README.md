# Codex of the Broken Pantheon — Fantasy Wiki

LAMP wiki for Pathfinder 1e campaigns. PHP 8.3, Apache 2, MariaDB, HTMX. Markdown via Parsedown + ParsedownExtra. Custom fenced blocks for statblocks, maps, and GM-only sections.

## Requirements

Alpine Linux VM (3.19+). Run as root.

## Setup

```sh
# 1. Clone / copy repo to the VM
git clone <your-repo> /var/www/wiki
cd /var/www/wiki

# 2. Create .env from template
cp .env.example .env
vi .env   # set passwords + WIKI_SECRET

# 3. Run setup script (installs packages, configures Apache + MariaDB, seeds DB)
sh setup.sh
```

The script:
- Installs `apache2`, `php83`, `mariadb` via apk
- Enables mod_rewrite + mod_headers
- Writes `/etc/apache2/conf.d/wiki.conf` pointing DocumentRoot at `src/public/`
- Creates the MariaDB DB + user + runs migrations + seeds example pages
- Enables both services on boot

**Default GM login:** `gm` / value of `WIKI_GM_PASS` in `.env`. Change after first login.

## nginxpm reverse proxy

In Nginx Proxy Manager, add a Proxy Host:
- **Forward Hostname/IP:** VM's IP address
- **Forward Port:** `80`

Enable SSL there if desired — the wiki itself speaks plain HTTP internally.

## .env reference

```sh
WIKI_DB_HOST=localhost
WIKI_DB_NAME=wiki
WIKI_DB_USER=wiki
WIKI_DB_PASS=strong_password

WIKI_GM_USER=gm
WIKI_GM_PASS=strong_gm_password

# openssl rand -hex 32
WIKI_SECRET=64_hex_chars

ALLOW_SIGNUP=0    # 1 to allow player self-registration
```

## Markdown syntax

### Statblock (Pathfinder 1e)
~~~
::: statblock
name: Orc Warrior
cr: 1
xp: 400
type: CE Medium humanoid (orc)
init: +0
senses: darkvision 60 ft.; Perception +1

# Defense
AC: 13, touch 10, flat-footed 13 (+3 armor)
HP: 6 (1d10+1)
Saves: Fort +3, Ref +0, Will -1

# Offense
Speed: 30 ft.
Melee: falchion +4 (2d4+4/18-20)

# Statistics
Str: 17
Dex: 11
Con: 12
Int: 7
Wis: 8
Cha: 6
BAB: +1
CMB: +4
CMD: 14
Feats: Weapon Focus (falchion)
Skills: Intimidate +2, Perception +1
Languages: Common, Orc
Gear: studded leather, falchion

# Special Abilities
**Ferocity (Ex).** An orc can continue fighting at 0 or fewer hp without being staggered.
:::
~~~

### Map block
~~~
::: map src=/uploads/yourmap.png caption="Region map" scale="10 leagues"
pin x=30 y=40 label="Town Name" cap=1
pin x=60 y=70 label="Dungeon"
:::
~~~
Omit `src` for a procedural parchment-map background.

### GM-only block
~~~
::: gm GM Eyes Only
Content only the GM sees. Body stripped from HTML for player sessions.
:::

::: gm[reveal] Hint (click to reveal)
Players can click to reveal this block.
:::
~~~

### Wiki links
```
[[Page Title]]
[[page-slug|Display Text]]
```
Red-links appear for pages that don't exist yet — click to create.

### Tags
```
tags: Undead, Karn-Vael, Boss
```

## Display settings

Click the ⚙ gear icon in the top bar: Theme (Parchment / Candle / Vellum), Density, Font pairing, and GM reveal toggle. Stored in a signed cookie.

## Manual DB management

```sh
# Re-run migrations after upgrade
mysql -u wiki -p wiki < src/migrations/001_init.sql

# Backup
mysqldump -u wiki -p wiki > backup.sql
```
