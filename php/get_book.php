<?php
    include_once("db-config.inc.php");
    $sslCa = __DIR__ . "/../certs/DigiCertGlobalRootCA.crt.pem";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
$options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;

// if ($env === 'production') {

//     $sslCa = __DIR__ . '/DigiCertGlobalRootCA.crt.pem';

//     $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
//     $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
// }

try {
    $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS, $options);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
   
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT books.title, books.author,books.cover_url, books.description, books.series FROM books WHERE id =".$_GET["id"];
    // $sql = "SELECT books.title, books.author,books.cover_url, reviews.rating, reviews.content, AVG(reviews.rating) as avg_rating FROM books INNER JOIN reviews ON books.id = reviews.book_id";
    $results = $pdo->query($sql);
    $rows = $results->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
?>