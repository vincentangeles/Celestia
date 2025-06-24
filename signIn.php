<?php
include("connect.php");

session_start();
session_destroy();
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $passwordInput = $_POST['password'];

    // Sanitize input (basic)
    $email = str_replace("'", "", $email);
    $passwordInput = str_replace("'", "", $passwordInput);

    // Query organizer
    $query = "SELECT * FROM organizers WHERE email = '$email'";
    $result = executeQuery($query);

    // Initialize session variables
    $_SESSION['organizerID'] = "";
    $_SESSION['organizerInfoID'] = "";
    $_SESSION['username'] = "";
    $_SESSION['email'] = "";
    $error = "";

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if ($row['isActive'] == 1) {
            if ($row['password'] == $passwordInput) {
                // Set organizer session data
                $_SESSION['organizerID'] = $row['organizerID'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $row['email'];

                // Get organizerInfoID from organizerinfo table
                $orgID = $row['organizerID'];
                $queryInfo = "SELECT organizerInfoID FROM organizerinfo WHERE organizerID = $orgID";
                $infoResult = executeQuery($queryInfo);
                if ($infoRow = mysqli_fetch_assoc($infoResult)) {
                    $_SESSION['organizerInfoID'] = $infoRow['organizerInfoID'];
                } else {
                    $_SESSION['organizerInfoID'] = null; // fallback
                }

                header("Location: eventsorg.php");
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Your account is not active.";
        }
    } else {
        $error = "Email not found.";
    }

    // Show error alert
    if (!empty($error)) {
        echo "<script>alert('$error'); window.location.href='signIn.php';</script>";
        exit();
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/signIn.css">
    <link rel="icon" href="assets/img/icon.gif">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100 justify-content-center">
            <div class="col-12 col-sm-8 col-md-6 col-lg-5">
                <div class="card m-4 p-5 border-0">
                    <h1 class="title text-center mb-5">Welcome Back!</h1>
                    <form action="signIn.php" method="post">
                        <div class="email mb-3">
                            <input type="email" class="form-control rounded-5" id="email" name="email"
                                placeholder="Email" required>
                        </div>
                        <div class="password mb-1">
                            <input type="password" class="form-control rounded-5" id="password" name="password"
                                placeholder="Password" required>
                        </div>
                        <div class="text-center mt-5">
                            <input type="submit" class="btn btn-custom" value="Sign In">
                        </div>
                        <div class="text-center mt-5 mb-5">
                            <span class="text-muted me-2">Not yet planning?</span>
                            <a href="signup.php" class="custom-link text-decoration-none">Create account</a>
                        </div>
                        <div class="text-center mt-5 mb-5">
                            <a href="index.php" class="custom-link text-decoration-none">Go Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>