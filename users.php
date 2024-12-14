<?php
session_start();
require 'db.php';

//check session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT id, username, email, mobile, gender FROM users");
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<html>
<title>Users</title>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h3 class="text-center">User List</h3>
        <a href="logout.php" class="btn btn-danger mb-3">Logout</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Gender</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $row): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['mobile']) ?></td>
                        <td><?= htmlspecialchars($row['gender']) ?></td>
                        <td>
                            <a href="add_user.php?id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">Add</a>
                            <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>