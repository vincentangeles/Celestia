<?php
include("connect.php");

session_start();
session_destroy();
session_start();

$firstname = $lastname = $birthdate = $address = $username = $email = "";
$passwordMismatch = false;
$showEmptyFieldsAlert = false;
$emailTaken = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $birthdate = trim($_POST['birthdate']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);

    if (
        empty($firstname) || empty($lastname) || empty($birthdate) ||
        empty($username) || empty($email) || empty($password) || empty($confirmPassword)
    ) {
        $showEmptyFieldsAlert = true;
    } elseif ($password !== $confirmPassword) {
        $passwordMismatch = true;
    } else {
        $check = executeQuery("SELECT * FROM organizers WHERE email = '$email' OR username = '$username'");
        if (mysqli_num_rows($check) > 0) {
            $emailTaken = true;
        } else {
            $now = date('Y-m-d H:i:s');
            $query = "INSERT INTO organizers (username, password, email, dateCreated, isActive) 
                      VALUES ('$username', '$password', '$email', '$now', 1)";
            $result = executeQuery($query);

            if ($result) {
                $getUserQuery = "SELECT * FROM organizers WHERE email = '$email' LIMIT 1";
                $getUserResult = executeQuery($getUserQuery);
                if (mysqli_num_rows($getUserResult) > 0) {
                    $user = mysqli_fetch_assoc($getUserResult);
                    $organizerID = $user['organizerID'];

                    $infoQuery = "INSERT INTO organizerinfo (organizerID, organizerFirstName, organizerLastName, birthdate)
                                  VALUES ($organizerID, '$firstname', '$lastname', '$birthdate')";
                    $infoResult = executeQuery($infoQuery);

                    if ($infoResult) {
                        $getInfoQuery = "SELECT organizerInfoID FROM organizerinfo WHERE organizerID = $organizerID LIMIT 1";
                        $getInfoResult = executeQuery($getInfoQuery);
                        $infoRow = mysqli_fetch_assoc($getInfoResult);

                        $_SESSION['organizerID'] = $organizerID;
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['organizerInfoID'] = $infoRow['organizerInfoID'];

                        header("Location: eventsorg.php");
                        exit();
                    } else {
                        $showEmptyFieldsAlert = true;
                    }
                }
            } else {
                $showEmptyFieldsAlert = true;
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/signUp.css">
    <link rel="icon" href="assets/img/icon.gif">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100 justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-7">
                <div class="card m-4 p-5 border-0">
                    <h1 class="title text-center mb-4">Plan with Us!</h1>

                    <!-- Alerts -->
                    <?php if ($showEmptyFieldsAlert): ?>
                        <div class="alert alert-warning text-center rounded-5">Please fill out all required fields.</div>
                    <?php endif; ?>

                    <?php if ($passwordMismatch): ?>
                        <div class="alert alert-danger text-center rounded-5">Passwords do not match.</div>
                    <?php endif; ?>

                    <?php if ($emailTaken): ?>
                        <div class="alert alert-danger text-center rounded-5">Email or Username is already taken.</div>
                    <?php endif; ?>

                    <form action="signUp.php" method="post">
                        <div class="row mb-3">
                            <div class="col">
                                <input type="text" class="form-control rounded-5" name="firstname"
                                    placeholder="First Name" value="<?= htmlspecialchars($firstname) ?>" required>
                            </div>
                            <div class="col">
                                <input type="text" class="form-control rounded-5" name="lastname"
                                    placeholder="Last Name" value="<?= htmlspecialchars($lastname) ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <input type="date" class="form-control rounded-5" name="birthdate"
                                    value="<?= htmlspecialchars($birthdate) ?>" required>
                            </div>
                            <div class="col">
                                <!-- Optional address field can go here if needed -->
                            </div>
                        </div>
                        <hr class="my-4 border-top border-secondary opacity-25">
                        <div class="row mb-3">
                            <div class="col">
                                <input type="text" class="form-control rounded-5" name="username" placeholder="Username"
                                    value="<?= htmlspecialchars($username) ?>" required>
                            </div>
                            <div class="col">
                                <input type="email" class="form-control rounded-5" name="email" placeholder="Email"
                                    value="<?= htmlspecialchars($email) ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <input type="password" class="form-control rounded-5" name="password"
                                    placeholder="Password" required>
                            </div>
                            <div class="col">
                                <input type="password" class="form-control rounded-5" name="confirm_password"
                                    placeholder="Confirm Password" required>
                            </div>
                        </div>
                        <div class="text-center mt-5">
                            <input type="submit" class="btn btn-custom" value="Sign Up">
                        </div>
                        <div class="text-center mt-5 mb-3">
                            <span class="text-muted me-2">Already Planning?</span>
                            <a href="signin.php" class="custom-link text-decoration-none">Sign in</a>
                        </div>
                        <div class="text-center mt-5 mb-5">
                            <a href="index.php" class="custom-link text-decoration-none">Go Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>