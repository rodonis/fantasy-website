-- Fantasy Wiki — initial schema
SET NAMES utf8mb4;
SET foreign_key_checks = 0;

CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(64)  UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('player','gm') NOT NULL DEFAULT 'player',
  display_name  VARCHAR(128),
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pages (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  slug        VARCHAR(160) UNIQUE NOT NULL,
  title       VARCHAR(255) NOT NULL,
  body_md     MEDIUMTEXT   NOT NULL,
  visibility  ENUM('public','gm') NOT NULL DEFAULT 'public',
  category    VARCHAR(64)  NOT NULL DEFAULT 'lore',
  updated_by  INT,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
  FULLTEXT KEY ft_search (title, body_md)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS revisions (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  page_id    INT NOT NULL,
  body_md    MEDIUMTEXT NOT NULL,
  user_id    INT,
  comment    VARCHAR(255),
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS links (
  from_slug VARCHAR(160) NOT NULL,
  to_slug   VARCHAR(160) NOT NULL,
  PRIMARY KEY (from_slug, to_slug),
  KEY idx_to (to_slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tags (
  page_id INT        NOT NULL,
  tag     VARCHAR(64) NOT NULL,
  PRIMARY KEY (page_id, tag),
  FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET foreign_key_checks = 1;
