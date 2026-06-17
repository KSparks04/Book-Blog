<?php
include_once("db-config.inc.php");
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
// Trim invisible whitespace from the edges of the input
$searchTerm = trim($_GET['info']);

// Optional: Limit the length so they don't send a 10,000-character string
$searchTerm = substr($searchTerm, 0, 100);


$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT books.id, books.title, books.author,books.cover_url FROM books WHERE books.title LIKE ? OR books.author LIKE ?";
$stmt = $pdo->prepare($sql);

$likeParameter = "%".$searchTerm."%";
$stmt->execute([$likeParameter,$likeParameter]);



$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows);
?>