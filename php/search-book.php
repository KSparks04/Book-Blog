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
function bookNeedsMetadata($book)
{
    return empty($book['author'])
        || empty($book['description'])
        || empty($book['page_count']);
}
function searchLocalBooks($title)
{

    global $pdo;

    $sql = "
        SELECT
            id,
            title,
            author,
            description,
            page_count,
            cover_url,
            work_key
        FROM books
        WHERE title LIKE ?
        LIMIT 10
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title . '%']);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function searchOpenLibrary($title)
{
    $url = 'https://openlibrary.org/search.json?' . http_build_query([
        'title' => $title,
        'limit' => 25,
        'fields' => 'key,title,author_key,author_name,isbn,cover_i,first_publish_year'
    ]);

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' =>
                "User-Agent: TheBookBlogClub/1.0 (kaiyadancer@hotmail.com)\r\n" .
                "Accept: application/json\r\n"
        ]
    ]);

    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        return null;
    }

    $data = json_decode($response, true);

    if (empty($data['docs'])) {
        return [];
    }

    $results = [];

    foreach ($data['docs'] as $book) {

        $results[] = [
            'source' => 'openlibrary',
            'work_key' => $book['key'] ?? null,
            'title' => $book['title'] ?? null,
            'author' => implode(', ', $book['author_name'] ?? []),
            'authors' => $book['author_name'] ?? [],
            'author_keys' => $book['author_key'] ?? [],
            'cover_url' => isset($book['cover_i'])
                ? "https://covers.openlibrary.org/b/id/"
                . $book['cover_i'] . "-L.jpg"
                : null,
            'first_publish_year' =>
                $book['first_publish_year'] ?? null
        ];
    }

    return $results;

}
function extractBookInfo($book)
{
    return [
        'work_key' => $book['key'] ?? null,
        'title' => $book['title'] ?? null,
        'authors' => $book['author_name'] ?? null,
        'author_keys' => $book['author_key'] ?? [],
        'isbn' => $book['isbn'][0] ?? null,
        'page_count' => $book['number_of_pages_median'] ?? null,
        'cover_url' => isset($book['cover_i'])
            ? "https://covers.openlibrary.org/b/id/{$book['cover_i']}-L.jpg"
            : null,
        'description' => null,
        'subjects' => $book['subject'] ?? [],
        'source' => 'openlibrary'
    ];

}
function updateBookMetadata($bookId, $data)
{
    global $pdo;

    $sql = "
        UPDATE books
        SET
           
            description = ?,
            page_count = ?,
            cover_url = ?,
            work_key = ?,
             metadata_fetched = 1
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([

        $data['description'],
        $data['page_count'],
        $data['cover_url'],
        $data['work_key'],
        $bookId
    ]);
    saveBookAuthors($bookId, $data);

}
function saveBook($data)
{
    global $pdo;

    $sql = "
        INSERT INTO books
            (
                title,
                description,
                page_count,
                cover_url,
                work_key,
                metadata_fetched
            )
        VALUES
            (?, ?, ?, ?, ?, ?)
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $data['title'],
        $data['author'],
        $data['description'],
        $data['page_count'],
        $data['cover_url'],
        $data['work_key'],
        1
    ]);
    $bookId = $pdo->lastInsertId();
    saveBookAuthors($bookId, $data);
    return $bookId;
}
function findOrCreateBook($title)
{
    $localBooks = searchLocalBooks($title);

    // We already have the book locally
    if (!empty($localBooks)) {

        $book = $localBooks[0];

        // It already has enough information
        if (!bookNeedsMetadata($book)) {
            return $book;
        }

        // It exists, but needs enrichment
        $data = searchOpenLibrary($title);

        if ($data && !empty($data['docs'])) {

            $bookData = extractBookInfo($data['docs'][0]);

            updateBookMetadata($book['id'], $bookData);

            return array_merge($book, $bookData);
        }

        return $book;
    }

    // Book doesn't exist locally
    $data = searchOpenLibrary($title);

    if (!$data || empty($data['docs'])) {
        return null;
    }

    $bookData = extractBookInfo($data['docs'][0]);

    $id = saveBook($bookData);

    $bookData['id'] = $id;

    return $bookData;
}
function findOrCreateAuthor($authorKey, $authorName)
{
    global $pdo;

    // Does the author already exist?
    $sql = "SELECT id FROM authors WHERE author_key = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$authorKey]);

    $author = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($author) {
        return $author['id'];
    }

    // Doesn't exist, so create them
    $sql = "
        INSERT INTO authors (author_key, name)
        VALUES (?, ?)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $authorKey,
        $authorName
    ]);

    return $pdo->lastInsertId();
}
function linkAuthorToBook($bookId, $authorId)
{
    global $pdo;

    $sql = "
        INSERT IGNORE INTO book_authors (book_id, author_id)
        VALUES (?, ?)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $bookId,
        $authorId
    ]);
}
function saveBookAuthors($bookId, $bookData)
{
    $authors = $bookData['authors'] ?? [];
    $authorKeys = $bookData['author_keys'] ?? [];

    foreach ($authors as $index => $authorName) {

        $authorKey = $authorKeys[$index] ?? null;

        if (!$authorKey || !$authorName) {
            continue;
        }

        $authorId = findOrCreateAuthor(
            $authorKey,
            $authorName
        );

        linkAuthorToBook(
            $bookId,
            $authorId
        );
    }
}
// $book = findOrCreateBook("Dune");

// if ($book === null) {
//     echo "Book not found.";
// } else {
//     print_r($book);
// }
// $data = searchOpenLibrary("Dune");

// // print_r($data);

// if ($data === null) {
//     echo "Open Library request failed.";
//     exit;
// }

// if (empty($data['docs'])) {
//     echo "No books found.";
//     exit;
// }

// $book = $data['docs'][0];

// echo "Title: " . ($book['title'] ?? 'N/A') . PHP_EOL;
// echo "Author: " . ($book['author_name'][0] ?? 'N/A') . PHP_EOL;
// echo "Work: " . ($book['key'] ?? 'N/A') . PHP_EOL;
// echo "ISBN: " . ($book['isbn'][0] ?? 'N/A') . PHP_EOL;
// echo "Pages: " . ($book['number_of_pages_median'] ?? 'N/A') . PHP_EOL;
// echo "Subject: " . print_r($book['subject'] ?? 'N/A') . PHP_EOL;
?>