<?php
require_once 'php/db.php';

try {
    // Add session_token column if it doesn't exist
    $conn->query("ALTER TABLE users ADD COLUMN session_token VARCHAR(64)");
    echo "Migration completed: session_token column added\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Migration skipped: session_token column already exists\n";
    } else {
        echo "Migration error: " . $e->getMessage() . "\n";
    }
}

$conn->close();
?>