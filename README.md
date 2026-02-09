For development I hosted the webapp in the HTdocs folder of xampp and created the databse in PHPmyadmin

to replicate the database run this sql:
CREATE DATABASE `log-in-system`;
USE `log-in-system`;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 0
);

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_owner_id VARCHAR(255) NOT NULL,
    post_content TEXT NOT NULL,
    post_image VARCHAR(255), - THIS IS A PLACEHOLDER AND VERY WRONG, I think it needs to be blob but im having trouble making it work
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    likes TINYINT(1) NOT NULL DEFAULT 0,
    dislikes TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE KEY unique_like (post_id, user_id)
);

CREATE TABLE reply (
    reply_id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    ping_owner TINYINT(1) NOT NULL DEFAULT 1
);
