<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!$conn->ping()) {
        die("Database connection was closed unexpectedly.");
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo 'email Id already exists';
        } else {
            echo 'available';
        }

        $stmt->close();
    } else {
        echo "Failed to prepare statement.";
    }
}
