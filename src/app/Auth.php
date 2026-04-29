<?php
declare(strict_types=1);

class Auth {
    public static function user(): ?array {
        if (!isset($_SESSION['user_id'])) return null;
        static $cache = null;
        if ($cache !== null) return $cache;
        $cache = Db::one('SELECT id, username, display_name, role FROM users WHERE id = ?', [$_SESSION['user_id']]);
        return $cache;
    }

    public static function role(): string {
        return self::user()['role'] ?? 'guest';
    }

    public static function isGm(): bool {
        return self::role() === 'gm';
    }

    public static function isLoggedIn(): bool {
        return self::user() !== null;
    }

    public static function requireLogin(): void {
        if (!self::isLoggedIn()) {
            header('Location: /login?next=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
    }

    public static function requireGm(): void {
        self::requireLogin();
        if (!self::isGm()) {
            http_response_code(403);
            echo 'Forbidden.';
            exit;
        }
    }

    public static function login(string $username, string $password): bool {
        $user = Db::one('SELECT * FROM users WHERE username = ?', [strtolower($username)]);
        if (!$user || !password_verify($password, $user['password_hash'])) return false;
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        return true;
    }

    public static function logout(): void {
        $_SESSION = [];
        session_destroy();
    }

    public static function register(string $username, string $password, string $role = 'player'): bool {
        $username = strtolower(trim($username));
        if (strlen($username) < 2 || strlen($password) < 6) return false;
        try {
            Db::run(
                'INSERT INTO users (username, password_hash, role, display_name) VALUES (?, ?, ?, ?)',
                [$username, password_hash($password, PASSWORD_DEFAULT), $role, $username]
            );
            return true;
        } catch (PDOException) {
            return false;
        }
    }
}
