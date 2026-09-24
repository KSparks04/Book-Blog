<?php
session_start();
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



$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 $sql = "SELECT * FROM books";
 $params = [];
if ($_GET['format'] == "books"){
    $sql = "SELECT * FROM books JOIN book_genres on books.id = book_genres.book_id WHERE 1=1";
$params = [];
$genres = $_GET['genres'] ?? [];
$tags = $_GET['tags'] ?? [];

if (!empty($genres)) {
     $placeholders = implode(',', array_fill(0, count($genres), '?'));

    $sql .= " AND book_genres.genre_id IN ($placeholders)";

    foreach ($genres as $genre) {
        $params[] = $genre;
    }
    
}
}else if($_GET['format'] === "posts"){
    $sql = "SELECT * FROM posts JOIN post_tags on posts.id = post_tags.post_id WHERE 1=1";
    if (!empty($tags)) {
     $placeholders = implode(',', array_fill(0, count($tags), '?'));

    $sql .= " AND post_tags.tag_id IN ($placeholders)";

    foreach ($tags as $tag) {
        $params[] = $tag;
    }
    
}
}


$stmt = $pdo->prepare($sql);


$stmt->execute($params);


$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($rows);
?>