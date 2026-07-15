<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include_once("php/db-config.inc.php");
$sslCa = __DIR__ . "/../certs/DigiCertGlobalRootCA.crt.pem";
$env = getenv('APP_ENV') ?: 'local';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

if ($env !== 'local') {
    $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
}
try {
    $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS, $options);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$email = trim($_POST['email']);

$password = $_POST['password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address");
}
if (strlen($password) < 8) {
    die("Password must be at least 8 characters.");
}
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    die("Email already exists.");
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);


$stmt = $pdo->prepare("
    INSERT INTO users (username,email, password_hash)
    VALUES (?,?, ?)
");

$stmt->execute([$_POST["username"], $email, $passwordHash]);
$user_id = $pdo->lastInsertId();
$_SESSION['user_id'] = $user_id;
$_SESSION['email'] = $email;
$_SESSION['logged_in'] = true;

header("Location:" . BASE_URL );

?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link href="css/base.css" rel="stylesheet">
    <link href="css/text-styles.css" rel="stylesheet">
    <link href="css/main-home.css" rel="stylesheet">
    <script src="js/main-home.js"></script>
    <title>The Book Blog Club</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
</head>

<body>
    <header id="header">

        <div id="title">
            <a href="./">
                <img id="logo" src="images/Book-Blog-Club-Logo-Transparent.png" alt="the book blog club logo">
                <!-- <h1>The Book Blog Club</h1> -->
            </a>


        </div>
        <nav id="nav-bar">
            <ul>
                <li class="nav-button"><a id="home" href="./">Home</a></li>
                <li class="nav-button"><a id="explore" href="index.php/explore">Browse</a></li>
                <?php
                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
                    echo '<li class="nav-button"><a id="user-posts" href="index.php/boards">My Boards</a></li>';
                }

                ?>

            </ul>
            <div id="user-create">
                <a id="signup"><i class="bi bi-person"></i></a>

                <div class="modal">
                    <div class="modal-container">
                        <span class="close">&times</span>

                        <!-- <div>
                            <h3>Create your account</h3>
                            <form id="sign-up" action="success-signup" method="post">
                                <label for="username">Displayed Username</label>
                                <input type="text" name="username" id="username">
                                <label for="email">Email Address</label>
                                <input type="text" name="email" id="email">
                                <p id="email-error"></p>
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password">
                                <p id="pwrd-error"></p>

                                <button type="submit" id="sign-up-btn">Sign up with email</button>
                            </form>
                            <a href="<?= BASE_URL ?>/userauth">
                              
                                <button id="google-signup">Continue with Google</button>
                            </a>
                        </div> -->

                        <?php

                        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {


                            echo "Logged in";

                        }
                        if (!isset($_SESSION['email'])) {
                            echo "<div>
                            <h3>Create your account</h3>
                            <form id=\"sign-up\" action=\"success-signup\" method=\"post\">
                                <label for=\"username\">Displayed Username</label>
                                <input type=\"text\" name=\"username\" id=\"username\">
                                <label for=\"email\">Email Address</label>
                                <input type=\"text\" name=\"email\" id=\"email\">
                                <p id=\"email-error\"></p>
                                <label for=\"password\">Password</label>
                                <input type=\"password\" name=\"password\" id=\"password\">
                                <p id=\"pwrd-error\"></p>

                                <button type=\"submit\" id=\"sign-up-btn\">Sign up with email</button>
                            </form>
                            <a href=\"<?= BASE_URL ?>/userauth\">
                                
                                <button id=\"google-signup\">Continue with Google</button>
                            </a>
                        </div>;";
                        }

                        ?>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>/profile">Profile</a>

            </div>

        </nav>
    </header>
    <div id="main-content">






    </div>

</body>

</html>