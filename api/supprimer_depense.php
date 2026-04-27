<?php
// ============================================================
// FICHIER : api/supprimer_depense.php
// RÔLE    : Supprimer une dépense par son ID.
//            Reçoit  : { "id": 42 }
//            Renvoie : { "succes": true } ou { "succes": false, "message": "..." }
// ============================================================

// SECURITE : Paramètres de sécurité de la session (HttpOnly, Secure, SameSite)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
header("Content-Type: application/json");
require_once "config.php";

if (!isset($_SESSION["id_utilisateur"])) {
    http_response_code(401);
    echo json_encode(["succes" => false, "message" => "Non connecté."]);
    exit;
}

$donnees = json_decode(file_get_contents("php://input"), true);

// Vérifier que l'ID est présent.
if (empty($donnees["id"])) {
    echo json_encode(["succes" => false, "message" => "ID de dépense manquant."]);
    exit;
}

$idDepense = (int)$donnees["id"];

// --- SUPPRIMER LA DÉPENSE ---
// DELETE FROM supprime la ligne. WHERE id = :id cible uniquement cette dépense.
$suppression = $pdo->prepare("DELETE FROM expenses WHERE id = :id");
$suppression->execute([":id" => $idDepense]);

// rowCount() vérifie si une ligne a bien été supprimée.
if ($suppression->rowCount() > 0) {
    echo json_encode(["succes" => true, "message" => "Dépense supprimée."]);
} else {
    echo json_encode(["succes" => false, "message" => "Dépense introuvable."]);
}
