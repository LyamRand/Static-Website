<?php
// Reporting maximal (développement)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Header HSTS (sécurité transport)
if (!empty($_SERVER['HTTPS'])) {
    header("Strict-Transport-Security: max-age=31536000");
}

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$host = 'localhost';
$db   = 'ebus2_projet01_ttsi18';
$user = 'st93ll48pppl';
$pass = 'l3!z0p=5zt';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo json_encode(["success" => true, "message" => "Connexion réussie !"]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}