<?php
// ============================================================
// FICHIER : api/creer_groupe.php
// RÔLE    : Créer un nouveau groupe et y ajouter le créateur.
//           Reçoit  : { "nom": "...", "icone": "🏠" }
//           Renvoie : { "succes": true, "groupe_id": 42, "code": "A4KZ2B" }
// ============================================================

// SECURITE : Paramètres de sécurité de la session (HttpOnly, Secure, SameSite)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();
header("Content-Type: application/json");
require_once "config.php";

if (!isset($_SESSION["id_utilisateur"])) {
    http_response_code(401); // 401 = authentification requise mais identifiants manquants ou incorrects
    echo json_encode(["succes" => false, "message" => "Non connecté."]);
    exit;
}


$idUtilisateur = $_SESSION["id_utilisateur"]; // Prends l'ID qui est déjà stocké dans la session pour le mettre dans une variable locale ($idUtilisateur)

// --- LECTURE DES DONNÉES ENVOYÉES PAR VUE.JS ---
// json_decode transforme les données JSON reçues en un tableau PHP utilisable
$donnees = json_decode(file_get_contents("php://input"), true);

if (empty($donnees["nom"])) {
    echo json_encode(["succes" => false, "message" => "Le nom du groupe est obligatoire."]);
    exit;
}

$nomGroupe = trim($donnees["nom"]); // "trim" permet de supprimer les espaces inutiles au début et à la fin du texte
$iconeGroupe = $donnees["icone"] ?? "💬"; // "??" = valeur de secours au cas où le front-end n'envoie pas d'icône

// --- GÉNÉRATION DU CODE D'INVITATION ---
// "uniqid()" crée un identifiant unique et "str_shuffle" le mélange.
// substr(..., 0, 6) garde seulement les 6 premiers caractères. strtoupper met tout en majuscules.
$codeUnique = strtoupper(substr(str_shuffle(uniqid()), 0, 6));

try {
    // --- TRANSACTION SQL ---
    // beginTransaction() dit à MySQL que l'on va faire plusieurs actions (ici : créer le groupe PUIS ajouter le membre)
    // Si l'une des actions échoue, on annule tout pour éviter d'avoir un groupe "fantôme" sans créateur si la 2ème requête plante
    $pdo->beginTransaction();

    // 1ère action : Création du groupe dans la table "groups"
    $insertion = $pdo->prepare("
        INSERT INTO groups (name, logo, code)
        VALUES (:nom, :logo, :code)
    ");
    $insertion->execute([
        ":nom" => $nomGroupe,
        ":logo" => $iconeGroupe,
        ":code" => $codeUnique
    ]);

    $idNouveauGroupe = $pdo->lastInsertId();

    // Ajouter le créateur comme premier membre
    $ajoutMembre = $pdo->prepare("
        INSERT INTO group_users (group_id, user_id)
        VALUES (:group_id, :user_id)
    ");
    $ajoutMembre->execute([
        ":group_id" => $idNouveauGroupe,
        ":user_id" => $idUtilisateur
    ]);

    // --- VALIDATION DE LA TRANSACTION ---
    // Si on arrive ici, les requêtes ont réussi
    // commit() dit à MySQL de sauvegarder les deux actions
    $pdo->commit();

    echo json_encode([
        "succes" => true,
        "groupe_id" => (int) $idNouveauGroupe,
        "code" => $codeUnique
    ]);
} catch (Exception $e) {
    // --- ANNULATION DE LA TRANSACTION ---
    // Si une erreur se produit dans le bloc "try", on saute directement ici
    // rollBack() annule tout ce qui a été fait depuis beginTransaction()
    $pdo->rollBack();

    http_response_code(500); // 500 = Erreur serveur interne (problème inattendu)
    echo json_encode([
        "succes" => false,
        "message" => "Erreur lors de la création du groupe.",
        "erreur" => $e->getMessage()
    ]);
}
