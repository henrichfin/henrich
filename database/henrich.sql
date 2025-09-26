-- Database and table setup for Task Manager
CREATE DATABASE IF NOT EXISTS henrich CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE henrich;

CREATE TABLE IF NOT EXISTS tasks (
id INT UNSIGNED NOT NULL AUTO_INCREMENT,
title VARCHAR(255) NOT NULL,
description TEXT NULL,
status ENUM('pending','in_progress','done') NOT NULL DEFAULT 'pending',
created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;
