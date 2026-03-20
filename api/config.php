<?php
// db.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); 

// Tes identifiants d'après ta capture précédente
$host = 'localhost';
$db   = 'ebus2_projet01_ttsi18';
$user = 'st93l148pppl';
$pass = 'l3!z0p=5zt';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die(json_encode(["success" => false, "error" => "Erreur de connexion"]));
}