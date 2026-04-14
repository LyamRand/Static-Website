<?php
require_once __DIR__ . '/config.php';

try {
    $stmt = $pdo->query("SHOW COLUMNS FROM expenses LIKE 'category'");
    $exists = $stmt->fetch();
    
    if (!$exists) {
        $pdo->exec("ALTER TABLE expenses ADD COLUMN category VARCHAR(50) DEFAULT 'Autres'");
        echo "Colonne 'category' ajoutée avec succès.\n";
    } else {
        echo "La colonne 'category' existe déjà.\n";
    }
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
?>
