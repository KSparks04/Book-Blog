<?php
include_once("db-config.inc.php");
include_once("../importer/genres.php");
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
function bookHasAuthors($bookId)
{
    global $pdo;

    $sql = "
        SELECT 1
        FROM book_authors
        WHERE book_id = ?
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$bookId]);

    return (bool) $stmt->fetchColumn();
}
function bookHasGenres($bookId)
{
    global $pdo;

    $sql = "
        SELECT 1
        FROM book_genres
        WHERE book_id = ?
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$bookId]);

    return (bool) $stmt->fetchColumn();
}
function bookHasEditions($workKey)
{
    global $pdo;

    $sql = "
        SELECT 1
        FROM book_editions
        WHERE work_key = ?
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$workKey]);

    return (bool) $stmt->fetchColumn();
}
function bookNeedsMetadata($book)
{
    if (empty($book['description'])) {
        return true;
    }

    if (empty($book['cover_url'])) {
        return true;
    }

    if (!bookHasAuthors($book['id'])) {
        return true;
    }
    if (!bookHasGenres($book['id'])) {
        return true;
    }
    if (!bookHasEditions($book['work_key'])) {
        return true;
    }


    return false;
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
        'fields' => 'key,title,author_key,author_name,isbn,number_of_pages_median,cover_i,first_publish_year,subject'
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

    return json_decode($response, true);

}
function getOpenLibraryAuthor($authorKey)
{
    $url = "https://openlibrary.org" . $authorKey . ".json";

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

    return json_decode($response, true);
}
function getOpenLibraryEditions($workKey)
{
    $url =
        "https://openlibrary.org"
        . $workKey
        . "/editions.json";

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

    return json_decode($response, true);
}
function getWorkEditions($workKey)
{
    $url = 'https://openlibrary.org/works/' .
        urlencode(basename($workKey)) .
        '/editions.json?limit=100';

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
        return [];
    }

    $data = json_decode($response, true);

    return $data['entries'] ?? [];
}
function extractEditionInfo($edition, $workKey)
{
    $coverUrl = null;

    if (!empty($edition['covers'])) {
        $coverId = $edition['covers'][0];

        $coverUrl =
            "https://covers.openlibrary.org/b/id/"
            . $coverId
            . "-L.jpg";
    }

    return [
        'edition_key' => $edition['key'] ?? null,
        'work_key' => $workKey,

        'isbn_10' => $edition['isbn_10'][0] ?? null,
        'isbn_13' => $edition['isbn_13'][0] ?? null,

        'page_count' => $edition['number_of_pages'] ?? null,
        'published_date' => $edition['publish_date'] ?? null,

        'cover_url' => $coverUrl,

        'has_english_edition' => 1
    ];
}
function getEditionsWorks($workKey)
{
    // $workKey = "/works/OL54151713M";
    $workKey = str_replace('/works/', '/books/', $workKey);
    // echo $workKey;
    $url = "https://openlibrary.org" . $workKey . ".json";

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

    $work = json_decode($response, true);

    return $work;
}
function saveEdition($editionData)
{
    global $pdo;

    $sql = "
        INSERT INTO book_editions
        (
            edition_key,
            work_key,
            isbn_10,
            isbn_13,
            page_count,
            published_date,
            has_english_edition
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            work_key = VALUES(work_key),
            isbn_10 = VALUES(isbn_10),
            isbn_13 = VALUES(isbn_13),
            page_count = VALUES(page_count),
            published_date = VALUES(published_date),
            has_english_edition = VALUES(has_english_edition)
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $editionData['edition_key'],
        $editionData['work_key'],
        $editionData['isbn_10'],
        $editionData['isbn_13'],
        $editionData['page_count'],
        $editionData['published_date'],
        $editionData['has_english_edition']
    ]);
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
    // var_dump($data);

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
function importBook($workKey)
{
    global $pdo;
    $pdo->beginTransaction();

    try {

        $sql = "
        SELECT id
        FROM books
        WHERE work_key = ?
        LIMIT 1
    ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$workKey]);

        $existingBook = $stmt->fetch(PDO::FETCH_ASSOC);


        $url = "https://openlibrary.org" . $workKey . ".json";

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

        $work = json_decode($response, true);

        if (!$work) {
            return null;
        }



        $title = $work['title'] ?? null;

        $description = null;
        //         var_dump($work);
//         echo '<pre>';
// var_dump($work['description'] ?? 'NO DESCRIPTION');
// echo '</pre>';
// exit;
        if (isset($work['description'])) {
            if (is_array($work['description'])) {
                $description = $work['description']['value'] ?? null;
            } else {
                $description = $work['description'];
            }
        }
        // var_dump($work);
        // var_dump($description);
        $coverUrl = null;

        if (!empty($work['covers'])) {
            $coverId = $work['covers'][0];

            $coverUrl =
                "https://covers.openlibrary.org/b/id/"
                . $coverId
                . "-L.jpg";
        }

        $series = null;

        foreach ($work['subjects'] ?? [] as $subject) {

            $subject = trim($subject);

            if (str_starts_with($subject, 'series:')) {

                $series = trim(
                    substr($subject, strlen('series:'))
                );

                $series = str_replace('_', ' ', $series);

                break;
            }
        }

        if ($existingBook) {

            $bookId = $existingBook['id'];
            if ($coverUrl) {
                $sql = "
    UPDATE books
    SET
        title = ?,
        description = ?,
        cover_url = ?,
        series = ?,
        metadata_fetched = 1 WHERE id = ?";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $title,
                    $description,
                    $coverUrl,
                    $series,
                    $bookId
                ]);
            } else {
                $sql = "UPDATE books SET title = ?,
        description = ?,
        series = ?,
        metadata_fetched = 1 WHERE id = ?";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $title,
                    $description,
                    $series,
                    $bookId
                ]);
            }




        } else {



            $sql = "INSERT INTO books(title,
    description,
    cover_url,
    work_key,
    series,
    metadata_fetched
)VALUES(?, ?, ?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $title,
                $description,
                $coverUrl,
                $workKey,
                $series,
                1
            ]);

            $bookId = $pdo->lastInsertId();
        }
//         var_dump($stmt->rowCount());
//         $stmt = $pdo->prepare("
//     SELECT description
//     FROM books
//     WHERE id = ?
// ");

// $stmt->execute([$bookId]);

// var_dump($stmt->fetchColumn());
// exit;
        // AUTHORS IMPORT
        $authors = [];

        foreach ($work['authors'] ?? [] as $authorData) {

            $authorKey = $authorData['author']['key'] ?? null;

            if (!$authorKey) {
                continue;
            }

            $author = getOpenLibraryAuthor($authorKey);

            if (!$author) {
                throw new Exception("Could not fetch author: " . $authorKey);
            }

            $authorName = $author['name'] ?? null;

            if (!$authorName) {
                continue;
            }

            $authors[] = [
                'key' => $authorKey,
                'name' => $authorName
            ];
        }
        $sql = "DELETE FROM book_authors WHERE book_id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$bookId]);
        foreach ($authors as $authorData) {

            $authorId = findOrCreateAuthor(
                $authorData['key'],
                $authorData['name']
            );

            linkAuthorToBook(
                $bookId,
                $authorId
            );
        }
        $genreIds = [];

        foreach ($work['subjects'] ?? [] as $subject) {

            $genreId = genreMapper($subject);
            // echo $subject.PHP_EOL;
            if (!$genreId) {
                continue;
            }

            // Prevent duplicates
            if (!in_array($genreId, $genreIds)) {
                $genreIds[] = $genreId;
            }
        }
        $sql = "DELETE FROM book_genres WHERE book_id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$bookId]);


        foreach ($genreIds as $genreId) {

            $sql = "INSERT IGNORE INTO book_genres (book_id, genre_id) VALUES (?, ?)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $bookId,
                $genreId
            ]);
        }



        // EDITIONS
        $editions = getWorkEditions($workKey);

        $editionCoverUrl = null;
        
        foreach ($editions as $edition) {

            $editionData = extractEditionInfo(
                $edition,
                $workKey
            );
            if (empty($description)) {
                $workEdition = getEditionsWorks($editionData['edition_key']);
                // var_dump($workEdition);
                // $editionsDesc[] = $workEdition['description'];

                if (isset($workEdition['description'])) {
                    if (is_array($workEdition['description'])) {
                        $description = $workEdition['description']['value'] ?? null;
                    } else {
                        $description = $workEdition['description'];
                    }
                }
            }



            if (!$editionData['edition_key']) {
                continue;
            }

            saveEdition($editionData);

            if (!$editionCoverUrl && $editionData['cover_url']) {
                $editionCoverUrl = $editionData['cover_url'];
            }
        }
        if (!$coverUrl && $editionCoverUrl) {
            $coverUrl = $editionCoverUrl;
        }
        // var_dump($description);
        $sql = "UPDATE books SET cover_url = ?, description = ? WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $coverUrl,
            $description,
            $bookId
        ]);
//           $stmt = $pdo->prepare("
//     SELECT description
//      FROM books
//     WHERE id = ?");

// $stmt->execute([$bookId]);

// var_dump($stmt->fetchColumn());
// exit;
        $pdo->commit();

        return $bookId;

    } catch (Exception $e) {

        $pdo->rollBack();

        throw $e;
    }


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
        $data['description'] ?? null,
        $data['page_count'] ?? null,
        $data['cover_url'] ?? null,
        $data['work_key'] ?? null,
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

    $sql = "SELECT id FROM authors WHERE author_key = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$authorKey]);

    $author = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($author) {
        return $author['id'];
    }

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