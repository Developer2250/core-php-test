<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    //validations
    $errors = [];

    if (empty($email)) {
        $errors['email'] = "Email is not an empty.";
    }

    if (empty($password)) {
        $errors['password'] = "Password is not an empty.";
    }

    if (empty($errors)) {

        //query to check the user
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: users.php");
                exit();
            } else {
                $errors['password'] = "Invalid password.";
            }
        } else {
            $errors['email'] = " Credentials  are not found, Please Register Once.";
        }
    }
}
?>

<html>
<title>Login</title>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div class="container mt-5">
        <h3 class="text-center">Login</h3>

        <form method="post" action="login.php">
            <div class="form-group mt-4">
                <label>Email</label>
                <input type="text" id="email" name="email" class="form-control">
                <?php if (isset($errors['email'])): ?>
                    <span class="error text-danger"><?= $errors['email'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group mt-4">
                <label>Password</label>
                <div class="input-group">
                    <input type="password" id="password" name="password" class="form-control">
                    <span class="input-group-text" id="togglePasswordIcon" style="cursor: pointer;" onclick="togglePassword()">
                        <i class="fa fa-eye" aria-hidden="true" id="toggleIcon"></i>
                    </span>
                </div>
                <?php if (isset($errors['password'])): ?>
                    <span class="error text-danger"><?= $errors['password'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group mt-4">
                <button class="btn btn-success" type="submit">Login</button>
                <a href="sign_up.php" class="btn btn-info" type="button">Register</a>
            </div>
        </form>
    </div>
    <script>
        //toggle hide show pwd
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var toggleIcon = document.getElementById("toggleIcon");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.remove("fa-eye");
                toggleIcon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.remove("fa-eye-slash");
                toggleIcon.classList.add("fa-eye");
            }
        }
    </script>
</body>

</html>