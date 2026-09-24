<?php
include_once("../php/db-config.inc.php");
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
// $sql = "SELECT genres.id, genres.name FROM genres";
// // $sql = "SELECT books.title, books.author,books.cover_url, reviews.rating, reviews.content, AVG(reviews.rating) as avg_rating FROM books INNER JOIN reviews ON books.id = reviews.book_id";
// $results = $pdo->query($sql);
// $rows = $results->fetchAll(PDO::FETCH_ASSOC);
// $genres = json_encode($rows);
// echo print_r($genres);
function genreMapper($subject)
{   
    global $pdo;
     $subject = trim($subject);

    $sql = "
        SELECT id
        FROM genres
        WHERE LOWER(name) = LOWER(?)
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$subject]);

    $genre = $stmt->fetch(PDO::FETCH_ASSOC);

    return $genre ? $genre['id'] : null;

}

// echo print_r(genreMapper("high fantasy"));


?>