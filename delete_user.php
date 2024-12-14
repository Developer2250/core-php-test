<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $user_id = (int) $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header("Location: users.php");
        exit();
    } else {
        echo "Error deleting user: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "No user ID provided!";
}

$conn->close();
