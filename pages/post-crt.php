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
    INSERT INTO comments ( post_id,user_id, content)
    VALUES (?, ?, ?)
");

    $stmt->execute([
      $_POST['post-id'],
        $_SESSION['user_id'],
        $_POST['details']
    ]);
    

}
header("Location:" . BASE_URL . "/view-post?id=".$_POST['post-id']);
?>