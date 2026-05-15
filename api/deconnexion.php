<?php
// ============================================================
// FICHIER : api/deconnexion.php
// RÔLE : Détruire la session de l'utilisateur
// ============================================================

// --- VÉRIFICATION DU COOKIE ---
// Si l'utilisateur n'a pas de cookie de session => déjà déconnecté
// On renvoie directement un succès sans recréer de cookie inutilement
if (!isset($_COOKIE[session_name()])) {
    header("Content-Type: application/json");
    echo json_encode(["succes" => true]);
    exit;
}

// On démarre la session pour pouvoir y accéder
// SECURITE : Paramètres de sécurité de la session (HttpOnly, Secure, SameSite)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();

// header JSON.
header("Content-Type: application/json");

// session_destroy() supprime toutes les données de la session côté serveur et l'utilisateur n'est plus connecté
session_destroy();

// Confirmer au JS que c'est tout est bon
echo json_encode(["succes" => true, "message" => "Déconnexion réussie."]);
