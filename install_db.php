<?php

// Include the database configuration file
require_once __DIR__ . '/config/db.php';

// Define the path to the SQL file
$sqlFile = __DIR__ . '/database/atm_db.sql';

// Check if the SQL file exists
if (!file_exists($sqlFile)) {
    die("Error: SQL file not found at {$sqlFile}");
}

// Read the SQL file content
$sql = file_get_contents($sqlFile);

// Check if reading the file was successful
if ($sql === false) {
    die("Error: Could not read SQL file content.");
}

try {
    // Execute the SQL queries
    // This will create the database and tables if they don't exist
    $pdo->exec($sql);
    echo "Database 'atm_db' and tables created successfully!";
} catch (PDOException $e) {
    // Catch and display any database errors during creation
    die("Database creation failed: " . $e->getMessage());
}

?>