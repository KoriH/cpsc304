<?php
include 'db_connection.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];


    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':username', $username);
    oci_execute($stmt);

    if ($row = oci_fetch_assoc($stmt)) {
        $error_message = "This username is already taken.";
    } else {


        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";

        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':username', $username);
        oci_bind_by_name($stmt, ':password', $password);

        oci_execute($stmt);

        header("Location: login.php?message=Account+created+successfully");
        exit();
    }
}


oci_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
<title>Computer Configuration Website</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .login-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        #carouselExampleSlidesOnly {
            position: relative;
            z-index: 0;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">Computer Configurer</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
    </form>
  </div>
</nav>


<div class="login-container">
    <h1>Create Account</h1>
    <?php if (isset($error_message)) : ?>
        <p><?php echo $error_message; ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br><br>

        <input type="submit" value="Create Account">
    </form>
    <button onclick="window.location.href='login.php';">Click here to Login</button>
</div>

</body>
</html>
