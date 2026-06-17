<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include_once("php/base.inc.php");
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




//$target_dir = "../images/uploads/board-bckgnds/";
$target_dir = __DIR__ . "/../images/uploads/board-bckgnds/";

$fileName = $_SESSION['user_id'] . '_' . uniqid() . '_' . basename($_FILES["board-img"]["name"]);
$target_file = $target_dir . $fileName;
$uploadDirWeb = "../images/uploads/board-bckgnds/";

$backgroundImagePath = null;
$uploadOk = 0;


$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
// if (empty($_FILES["board-img"]["name"]) || empty($_POST['default_img'])) {

//     header("Location:" . BASE_URL);
//     exit;
// }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_FILES["board-img"]["name"])) {

        $check = getimagesize($_FILES["board-img"]["tmp_name"]);
        if ($check !== false) {
            //echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
            if (move_uploaded_file($_FILES["board-img"]["tmp_name"], $target_file)) {
                $backgroundImagePath = $uploadDirWeb;
            }
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }

}
if (!empty($_POST['default_img'])) {
    $backgroundImagePath = "../images/default-board-bckgnds/board-bkgnd".$_POST['default_img'].".jpg";
}

$stmt = $pdo->prepare("
    INSERT INTO boards (user_id, name, description,background_image)
    VALUES (?, ?, ?, ?)
");

$stmt->execute([
    $_SESSION['user_id'],
    $_POST['board-title'],
    $_POST['description'],
    $backgroundImagePath
]);

$boardId = $pdo->lastInsertId();
$stmt = $pdo->prepare("
    INSERT INTO board_tags (board_id, tag_id)
VALUES (?, ?);
");
foreach ($_POST['tag_id'] as $tag) {

    $stmt->execute([
        $boardId,
        $tag
    ]);

}



?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="../css/base.css" rel="stylesheet">
    <link href="../css/create-board.css" rel="stylesheet">

    <title>The Book Blog Club</title>
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
</head>

<body>
    <header id="header">

        <div id="title">
            <a href="../">
                <img id="logo" src="../images/Book-Blog-Club-Logo-Transparent.png" alt="the book blog club logo">
            </a>
        </div>
        <nav id="nav-bar">
            <ul>
                <li class="nav-button"><a id="home" href="../">Home</a></li>
                <li class="nav-button"><a id="explore" href="../index.php/explore">Browse</a></li>
                <?php
                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
                    echo '<li class="nav-button"><a id="user-posts" href="user-posts.html">My Posts</a></li>';
                }

                ?>
            </ul>
            <div id="user-create">
                <a id="signup" href="<?= BASE_URL ?>/signup">Login</a>
            </div>

        </nav>
    </header>
    <?php
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
        header("Location:" . BASE_URL . "/signup");
    }

    ?>
    <div>
        <h3>Board Created</h3>
        <a href="<?= BASE_URL ?>/boards">View Boards</a>
        <a>Return to Home Page</a>
    </div>

</body>

</html>