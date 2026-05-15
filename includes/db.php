<?php
// includes/db.php - Simple database connection
require_once __DIR__ . '/functions.php';

// Just return the connection from functions.php
function getDBConnection() {
    return getDB();
}