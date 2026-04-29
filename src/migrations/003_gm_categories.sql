SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  slug        VARCHAR(64) UNIQUE NOT NULL,
  name        VARCHAR(128) NOT NULL,
  created_by  INT,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO categories (slug, name)
SELECT DISTINCT category, CONCAT(UCASE(LEFT(REPLACE(REPLACE(category, '-', ' '), '_', ' '), 1)), SUBSTRING(REPLACE(REPLACE(category, '-', ' '), '_', ' '), 2))
FROM pages
WHERE category <> '';
