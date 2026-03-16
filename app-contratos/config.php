<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// app-contratos/config.php

$host = "192.185.214.25"; // "srv24.prodns.com.br";
$user = "eventoss_vocegov";
$pass = "Senh@2025";
$db = "eventoss_vocegov";

// Chave Secreta para integração via SSO (Deve ser a mesma do Portal)
define('SSO_SECRET_KEY', 'GestorGov_Secure_Integration_Token_2026!');

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 3,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
}
catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
