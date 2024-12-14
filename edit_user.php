<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: users.php");
    exit();
}
$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $gender = $_POST['gender'];


    //validations
    $errors =  [];
    if (empty($username)) {
        $errors['username'] = "Username cannot be empty.";
    }

    if (empty($email)) {
        $errors['email'] = "Email cannot be empty.";
    }

    if (!preg_match('/^\d{10}$/', $mobile)) {
        $errors['mobile'] = "Mobile number must be 10 digits.";
    }

    if (empty($gender)) {
        $errors['gender'] = "Gender is required.";
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors['email'] = "Email is already registered.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, mobile = ?, gender = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $username, $email, $mobile, $gender, $id);
        $stmt->execute();

        //redirection
        header("Location: users.php");
        exit();
    }
}

//display selected data
$result = $conn->query("SELECT * FROM users WHERE id = $id");

if ($result->num_rows === 0) {
    header("Location: users.php");
    exit();
}
$user = $result->fetch_assoc();
?>


<html>
<title>Edit User</title>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h3 class="text-center">Edit User</h3>
        <form method="post" action="edit_user.php?id=<?= $user['id'] ?>">

            <div class="form-group mt-3">
                <label>Username</label>
                <input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>">
                <?php if (isset($errors['username'])): ?>
                    <span class="error text-danger"><?= $errors['username'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group mt-3">
                <label>Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>">
                <span id="email-error" class="error text-danger"></span>
            </div>


            <div class="form-group mt-3">
                <label>Mobile</label>
                <input type="text" id="mobile" name="mobile" class="form-control" value="<?= htmlspecialchars($user['mobile']) ?>">
                <?php if (isset($errors['mobile'])): ?>
                    <span class="error text-danger"><?= $errors['mobile'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group mt-3">
                <label>Gender</label><br>
                <input type="radio" name="gender" value="male" <?= $user['gender'] == 'male' ? 'checked' : '' ?>> Male
                <input type="radio" name="gender" value="female" <?= $user['gender'] == 'female' ? 'checked' : '' ?>> Female
                <?php if (isset($errors['gender'])): ?>
                    <span class="error text-danger"><?= $errors['gender'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group mt-4">
                <button class="btn btn-primary" type="submit">Update</button>
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