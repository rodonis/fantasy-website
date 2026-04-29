<?php
declare(strict_types=1);

class Db {
    private static ?PDO $pdo = null;

    public static function get(): PDO {
        if (self::$pdo !== null) return self::$pdo;
        $host = $_ENV['WIKI_DB_HOST'] ?? getenv('WIKI_DB_HOST') ?: 'wiki-db';
        $name = $_ENV['WIKI_DB_NAME'] ?? getenv('WIKI_DB_NAME') ?: 'wiki';
        $user = $_ENV['WIKI_DB_USER'] ?? getenv('WIKI_DB_USER') ?: 'wiki';
        $pass = $_ENV['WIKI_DB_PASS'] ?? getenv('WIKI_DB_PASS') ?: '';
        $dsn  = "mysql:host={$host};dbname={$name};charset=utf8mb4";
        self::$pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return self::$pdo;
    }

    public static function run(string $sql, array $params = []): PDOStatement {
        $stmt = self::get()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function one(string $sql, array $params = []): ?array {
        $row = self::run($sql, $params)->fetch();
        return $row ?: null;
    }

    public static function all(string $sql, array $params = []): array {
        return self::run($sql, $params)->fetchAll();
    }
}
