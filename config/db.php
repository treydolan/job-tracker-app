<?php
// /config/db.php

$host = "127.0.0.1";
$db   = "job_tracker";
$user = "root";
$pass = "";
$charset = "utf8mb4";

try {
    // Connect to MySQL server first
    $serverDsn = "mysql:host=$host;charset=$charset";
    $pdo = new PDO($serverDsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Create database if missing
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");

    // Connect to the database
    $dbDsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dbDsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Create companies table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS companies (
            company_id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL
        )
    ");

    // Create jobs table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS jobs (
            job_id INT AUTO_INCREMENT PRIMARY KEY,
            company_id INT NOT NULL,
            title VARCHAR(100) NOT NULL,
            status VARCHAR(50) NOT NULL,
            applied_date DATE NOT NULL,
            FOREIGN KEY (company_id) REFERENCES companies(company_id)
        )
    ");

} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}