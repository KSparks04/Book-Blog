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
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $stmt = $pdo->prepare("
    INSERT INTO posts (user_id, board_id, title, content,colour)
    VALUES (?, ?, ?, ?,?)
");

    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['boards-select'],
        $_POST['post-title'],
        $_POST['blog_content'],
        $_POST['color-sel']
    ]);

    $postId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("
    INSERT INTO post_tags (post_id, tag_id)
VALUES (?, ?);
");
    foreach ($_POST['tag_id'] as $tag) {

        $stmt->execute([
            $postId,
            $tag
        ]);

    }
    $stmt = $pdo->prepare("
    INSERT INTO post_books (post_id, book_id)
VALUES (?, ?);
");
    foreach ($_POST['books'] as $book) {

        $stmt->execute([
            $postId,
            $book
        ]);

    }

}
header("Location:" . BASE_URL . "/view-board?id=".$_POST['boards-select']);
?>