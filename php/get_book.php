<?php
include_once("db-config.inc.php");
include_once("import-book.php");
$sslCa = __DIR__ . "/../certs/DigiCertGlobalRootCA.crt.pem";
$env = getenv('APP_ENV') ?: 'local';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
if ($env === 'production') {
    $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
}


try {
    $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS, $options);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}


$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
function getBook($bookId)
{
    global $pdo;

    $sql = "
        SELECT
            id,
            title,
            description,
            page_count,
            cover_url,
            work_key,
            metadata_fetched,
            series
        FROM books
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$bookId]);

    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        return null;
    }
    $needsMetaUpdate = bookNeedsMetadata($book);

    if (!$book['metadata_fetched'] && !$needsMetaUpdate) {

        $data = searchOpenLibrary($book['title']);

        if ($data && !empty($data['docs'])) {

            $bookData = extractBookInfo($data['docs'][0]);

            updateBookMetadata(
                $book['id'],
                $bookData
            );

            // Reload the book so we return the updated data
            $stmt->execute([$bookId]);
            $book = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    return $book;
}
$bookId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$bookKey = $_GET['key'] ?? null;

if (!$bookId && !$bookKey) {
    http_response_code(400);

    echo json_encode([
        'error' => 'Missing book ID or Open Library work key'
    ]);

    exit;
}

if ($bookKey) {

    $bookId = importBook($bookKey);

    if (!$bookId) {
        http_response_code(404);

        echo json_encode([
            'error' => 'Could not import book'
        ]);

        exit;
    }
}

$book = getBook($bookId);

if (!$book) {
    http_response_code(404);

    echo json_encode([
        'error' => 'Book not found'
    ]);

    exit;
}

header('Content-Type: application/json');

echo json_encode($book);

?>