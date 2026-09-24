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

$searchTerm = trim($_GET['info']);


$searchTerm = substr($searchTerm, 0, 100);




$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

$limit = 25;
$offset = ($page - 1) * $limit;
$limitNum = max(1, (int) $limit);
$offsetNum = max(0, (int) $offset);

// $sql = "
//     SELECT DISTINCT
//         books.id,
//         books.title,
//         books.cover_url
//     FROM books
//     LEFT JOIN book_authors
//         ON books.id = book_authors.book_id
//     LEFT JOIN authors
//         ON book_authors.author_id = authors.id
//     WHERE books.title LIKE ?
//        OR authors.name LIKE ?
//     ORDER BY books.id
//     LIMIT $limitNum OFFSET $offsetNum
// ";





// // $sql = "SELECT books.id, books.title, books.author,books.cover_url FROM books WHERE books.title LIKE ? OR books.author LIKE ? ";
// $stmt = $pdo->prepare($sql);

// $likeParameter = $searchTerm . "%";


// $stmt->execute([$likeParameter, $likeParameter]);



// $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
function searchTitles($searchTerm)
{
    global $limitNum, $offsetNum, $pdo;
    $titleSql = "
        SELECT
            books.id,
            books.title,
            books.cover_url,
            books.work_key,
            GROUP_CONCAT(DISTINCT authors.name SEPARATOR ', ') AS author
        FROM books
        LEFT JOIN book_authors
            ON books.id = book_authors.book_id
        LEFT JOIN authors
            ON book_authors.author_id = authors.id
        WHERE books.title LIKE ?
        GROUP BY books.id, books.title, books.cover_url
        ORDER BY books.id
        LIMIT $limitNum OFFSET $offsetNum
    ";

    $stmt = $pdo->prepare($titleSql);
    $stmt->execute([$searchTerm . "%"]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as &$row) {
        $row['source'] = 'local';
    }

    return $rows;

}
function searchAuthors($searchTerm)
{
    global $limitNum, $offsetNum, $pdo;
    $authorSql = "
        SELECT
            books.id,
            books.title,
            books.cover_url,
            books.work_key,
            GROUP_CONCAT(DISTINCT authors.name SEPARATOR ', ') AS author
        FROM authors
        INNER JOIN book_authors
            ON authors.id = book_authors.author_id
        INNER JOIN books
            ON books.id = book_authors.book_id
        WHERE authors.name LIKE ?
        GROUP BY books.id, books.title, books.cover_url
        ORDER BY books.id
        LIMIT $limitNum OFFSET $offsetNum
    ";

    $stmt = $pdo->prepare($authorSql);
    $stmt->execute([$searchTerm . "%"]);
  
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as &$row) {
        $row['source'] = 'local';
    }

    return $rows;
}

$rows = searchTitles($searchTerm);

if (empty($rows)) {
    $rows = searchAuthors($searchTerm);
}

if (empty($rows)) {
    $rows = searchOpenLibrary($searchTerm);
}

header('Content-Type: application/json');
echo json_encode($rows);
?>