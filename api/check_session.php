<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

session_start();

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        "connected" => true,
        "user" => [
            "id" => $_SESSION['user_id'],
            "name" => $_SESSION['user_name'],
            "email" => $_SESSION['user_email']
        ]
    ]);
} else {
    echo json_encode(["connected" => false]);
}
