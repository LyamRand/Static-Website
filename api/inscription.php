<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Inclure la connexion à la base de données
require_once 'config.php';

// Récupérer les données brutes envoyées par le fetch Javascript (Vue)
$json = file_get_contents("php://input");
$data = json_decode($json);

// Vérifier que tous les champs sont présents
if (!$data || empty(trim($data->nom)) || empty(trim($data->email)) || empty(trim($data->password))) {
    echo json_encode(["success" => false, "error" => "Veuillez remplir tous les champs obligatoires."]);
    exit;
}

try {
    // 1. Vérifier si l'email existe déjà dans la base
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $data->email]);
    if ($stmt->fetch()) {
        echo json_encode(["success" => false, "error" => "Cet email est déjà utilisé par un autre compte."]);
        exit;
    }

    // 2. Hasher le mot de passe pour la sécurité
    $passwordHash = password_hash($data->password, PASSWORD_DEFAULT);

    // 3. Insérer le nouvel utilisateur dans la base de données
    $stmt = $pdo->prepare("INSERT INTO users (name, email, pwd_hash) VALUES (:name, :email, :pwd_hash)");
    $stmt->execute([
        'name' => $data->nom,
        'email' => $data->email,
        'pwd_hash' => $passwordHash
    ]);

    // Succès !
    echo json_encode(["success" => true, "message" => "Inscription enregistrée avec succès."]);

} catch (PDOException $e) {
    // Attraper les erreurs SQL potentielles (ex: nom de colonne incorrect)
    $errorMsg = $e->getMessage();
    if (strpos($errorMsg, 'Unknown column') !== false) {
        echo json_encode(["success" => false, "error" => "Erreur de noms de colonnes : " . $errorMsg]);
    } else {
        echo json_encode(["success" => false, "error" => "Erreur de base de données : " . $errorMsg]);
    }
}
