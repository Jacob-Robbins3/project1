<?php
require_once __DIR__ . '/config/db.php';

try {
    // Just a simple query to confirm connection works
    $stmt = $pdo->query("SELECT NOW()");
    $time = $stmt->fetchColumn();
    echo "✅ Database connection successful! Server time: " . $time;
} catch (Exception $e) {
    echo "❌ Database test failed: " . $e->getMessage();
}
?>
