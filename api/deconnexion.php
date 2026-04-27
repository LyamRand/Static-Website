<?php
// ============================================================
// FICHIER : api/deconnexion.php
// RÔLE    : Détruire la session de l'utilisateur (le déconnecter).
// ============================================================

// SECURITE : Ne pas recréer de cookie si l'utilisateur est déjà déconnecté
if (!isset($_COOKIE[session_name()])) {
    header("Content-Type: application/json");
    echo json_encode(["succes" => true]);
    exit;
}

// On démarre la session pour pouvoir y accéder.
// SECURITE : Paramètres de sécurité de la session (HttpOnly, Secure, SameSite)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();

// header JSON.
header("Content-Type: application/json");

// session_destroy() supprime toutes les données de la session côté serveur.
// L'utilisateur n'est plus reconnu comme connecté.
session_destroy();

// On confirme au JavaScript que c'est bon.
echo json_encode(["succes" => true, "message" => "Déconnexion réussie."]);
