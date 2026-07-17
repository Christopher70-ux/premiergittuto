<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'premiergituto');

function db_connect()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    if ($conn->connect_errno) {
        return null;
    }

    if (!$conn->select_db(DB_NAME)) {
        $dbName = $conn->real_escape_string(DB_NAME);
        if (!$conn->query("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
            return null;
        }
        if (!$conn->select_db(DB_NAME)) {
            return null;
        }
    }

    if (!$conn->set_charset('utf8mb4')) {
        return null;
    }

    return $conn;
}

function db_init($conn)
{
    $sql = "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `nom` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `email_unique` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    return $conn->query($sql) !== false;
}
