<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];
    $mobile = trim($_POST['mobile']);
    $gender = isset($_POST['gender']) ? $_POST['gender'] : null;


    //validations
    $errors = [];

    if (empty($username)) {
        $errors['username'] = "Username cannot be empty.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors['username'] = "Username already exists.";
        }
        $stmt->close();
    }

    if (empty($email)) {
        $errors['email'] = "Email cannot be empty.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors['email'] = "Email is already registered.";
        }
        $stmt->close();
    }

    if (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters.";
    }
    if ($password !== $confirmPassword) {
        $errors['confirm-password'] = "Passwords do not match.";
    }

    if (!preg_match('/^\d{10}$/', $mobile)) {
        $errors['mobile'] = "Mobile number must be 10 digits.";
    }

    if (empty($gender)) {
        $errors['gender'] = "Gender is required.";
    }

    if (empty($errors)) {
        //password hashing 
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        //insert query
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, mobile, gender) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $hashedPassword, $mobile, $gender);
        $stmt->execute();
        $stmt->close();

        //redirection
        header("Location: users.php");
    }
}
?>

<html>
<title>Add User </title>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h3 class="text-center">Add User</h3>

        <form method="post" action="add_user.php">
            <div class="form-group mt-4">
                <label>Username </label>
                <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($_SESSION['old_username'] ?? ''); ?>">
                <?php if (isset($errors['username'])): ?>
                    <span class="error text-danger"><?= $errors['username'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group mt-4">
                <label>Email</label>
                <input type="text" id="email" name="email" class="form-control">
                <?php if (isset($errors['email'])): ?>
                    <span class="error text-danger"><?= $errors['email'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group mt-4">
                <label>Password</label>
                <input type="password" id="password" name="password" class="form-control">
                <?php if (isset($errors['password'])): ?>
                    <span class="error text-danger"><?= $errors['password'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group mt-4">
                <label>Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" class="form-control">
                <?php if (isset($errors['confirm-password'])): ?>
                    <span class="error text-danger"><?= $errors['confirm-password'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group mt-4">
                <label>Mobile</label>
                <input type="text" id="mobile" name="mobile" class="form-control">
                <?php if (isset($errors['mobile'])): ?>
                    <span class="error text-danger"><?= $errors['mobile'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group mt-4">
                <label>Gender</label>
                <input type="radio" name="gender" value="male"> Male
                <input type="radio" name="gender" value="female"> Female
                <?php if (isset($errors['gender'])): ?>
                    <span class="error text-danger"><?= $errors['gender'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group mt-4">
                <button class="btn btn-success" type="submit">Submit</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('#email').on('blur', function() {
                const email = $(this).val();
                if (email) {
                    $.ajax({
                        url: 'check_email.php',
                        type: 'POST',
                        data: {
                            email
                        },
                        success: function(response) {
                            if (response === 'exists') {
                                $('#email-error').text('Email is already registered');
                            } else {
                                $('#email-error').text('');
                            }
                        }
                    });
                }
            });

            $('#create-account-form').on('submit', function(event) {
                let valid = true;

                if ($('#password').val() !== $('#confirm-password').val()) {
                    $('#confirm-password-error').text('Passwords do not match');
                    valid = false;
                } else {
                    $('#confirm-password-error').text('');
                }

                const mobile = $('#mobile').val();
                if (mobile.length !== 10 || isNaN(mobile)) {
                    $('#mobile-error').text('Mobile number must be 10 digits');
                    valid = false;
                } else {
                    $('#mobile-error').text('');
                }

                if (!valid) event.preventDefault();
            });
        });
    </script>
</body>

</html>