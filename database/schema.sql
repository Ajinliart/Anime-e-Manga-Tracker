-- Anime & Manga Tracker - schema del database
-- Importare prima questo file, poi seed.sql

CREATE DATABASE IF NOT EXISTS anime_tracker
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE anime_tracker;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS user_manga, user_anime, manga_genres, anime_genres, manga, anime, genres, users;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------
-- Utenti
-- ---------------------------------------------------------------
CREATE TABLE users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(30)  NOT NULL,
  email         VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_username (username),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Generi (condivisi da anime e manga)
-- ---------------------------------------------------------------
CREATE TABLE genres (
  id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  UNIQUE KEY uq_genres_name (name)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Catalogo
-- ---------------------------------------------------------------
CREATE TABLE anime (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(200) NOT NULL,
  synopsis    TEXT NULL,
  cover_url   VARCHAR(500) NULL,
  episodes    SMALLINT UNSIGNED NULL,
  year        SMALLINT UNSIGNED NULL,
  status_air  ENUM('in_corso', 'concluso') NOT NULL DEFAULT 'concluso',
  studio      VARCHAR(150) NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_anime_title (title),
  KEY idx_anime_year (year)
) ENGINE=InnoDB;

CREATE TABLE manga (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(200) NOT NULL,
  synopsis    TEXT NULL,
  cover_url   VARCHAR(500) NULL,
  chapters    SMALLINT UNSIGNED NULL,
  volumes     SMALLINT UNSIGNED NULL,
  year        SMALLINT UNSIGNED NULL,
  status_pub  ENUM('in_corso', 'concluso', 'in_pausa') NOT NULL DEFAULT 'concluso',
  author      VARCHAR(150) NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_manga_title (title),
  KEY idx_manga_year (year)
) ENGINE=InnoDB;

CREATE TABLE anime_genres (
  anime_id INT UNSIGNED NOT NULL,
  genre_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (anime_id, genre_id),
  KEY idx_anime_genres_genre (genre_id),
  CONSTRAINT fk_ag_anime FOREIGN KEY (anime_id) REFERENCES anime(id) ON DELETE CASCADE,
  CONSTRAINT fk_ag_genre FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE manga_genres (
  manga_id INT UNSIGNED NOT NULL,
  genre_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (manga_id, genre_id),
  KEY idx_manga_genres_genre (genre_id),
  CONSTRAINT fk_mg_manga FOREIGN KEY (manga_id) REFERENCES manga(id) ON DELETE CASCADE,
  CONSTRAINT fk_mg_genre FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Liste personali
-- status: planned = "Voglio guardarlo/leggerlo", in_progress = "In corso", completed = "Completato"
-- ---------------------------------------------------------------
CREATE TABLE user_anime (
  user_id    INT UNSIGNED NOT NULL,
  anime_id   INT UNSIGNED NOT NULL,
  status     ENUM('planned', 'in_progress', 'completed') NOT NULL,
  score      TINYINT UNSIGNED NULL,
  progress   SMALLINT UNSIGNED NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, anime_id),
  KEY idx_user_anime_anime (anime_id),
  CONSTRAINT chk_user_anime_score CHECK (score IS NULL OR score BETWEEN 1 AND 10),
  CONSTRAINT fk_ua_user  FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_ua_anime FOREIGN KEY (anime_id) REFERENCES anime(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE user_manga (
  user_id    INT UNSIGNED NOT NULL,
  manga_id   INT UNSIGNED NOT NULL,
  status     ENUM('planned', 'in_progress', 'completed') NOT NULL,
  score      TINYINT UNSIGNED NULL,
  progress   SMALLINT UNSIGNED NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, manga_id),
  KEY idx_user_manga_manga (manga_id),
  CONSTRAINT chk_user_manga_score CHECK (score IS NULL OR score BETWEEN 1 AND 10),
  CONSTRAINT fk_um_user  FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_um_manga FOREIGN KEY (manga_id) REFERENCES manga(id) ON DELETE CASCADE
) ENGINE=InnoDB;
