<?php
// ============================================================
// FICHIER : api/deconnexion.php
// RÔLE    : Détruire la session de l'utilisateur (le déconnecter).
// ============================================================

// On démarre la session pour pouvoir y accéder.
session_start();

// header JSON.
header("Content-Type: application/json");

// session_destroy() supprime toutes les données de la session côté serveur.
// L'utilisateur n'est plus reconnu comme connecté.
session_destroy();

// On confirme au JavaScript que c'est bon.
echo json_encode(["succes" => true, "message" => "Déconnexion réussie."]);
