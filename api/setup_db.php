<?php
require_once 'config.php';

try {
    // Check if column exists by trying to add it
    $stmt = $pdo->query("SHOW COLUMNS FROM `groups` LIKE 'code'");
    $exists = $stmt->fetch();
    
    if (!$exists) {
        $pdo->exec("ALTER TABLE `groups` ADD COLUMN `code` VARCHAR(10) DEFAULT NULL UNIQUE AFTER `logo`");
        echo "Column 'code' added successfully.\n";
        
        $stmt = $pdo->query("SELECT id FROM `groups` WHERE `code` IS NULL OR `code` = ''");
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $updateStmt = $pdo->prepare("UPDATE `groups` SET `code` = :code WHERE id = :id");
        
        $count = 0;
        foreach ($groups as $group) {
            // Function to generate a random 6-character code
            $code = '';
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            for ($i = 0; $i < 6; $i++) {
                $code .= $chars[mt_rand(0, strlen($chars) - 1)];
            }
            
            try {
                $updateStmt->execute(['code' => $code, 'id' => $group['id']]);
                $count++;
            } catch (PDOException $e) {
                // If collision simply ignore for now or generate another
            }
        }
        echo "Updated $count existing groups with unique codes.\n";
    } else {
        echo "Column 'code' already exists.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
