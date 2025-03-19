<?php
session_start();

// Redirect to login if session is not set
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';  // Include the database connection

// Check if ID is passed for deletion
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the item from the database
    $query = "DELETE FROM inventory WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Redirect back to inventory page after deletion
header("Location: inventory.php");
exit();
?>
