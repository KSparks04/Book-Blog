<?php
include_once("db-config.inc.php");
$pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
set_time_limit(0); // 5 minutes


function fetchData($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "User-Agent: Mozilla/5.0"
    ]);

    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        die("cURL error: " . curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 400) {
        die("HTTP error: " . $httpCode);
    }

    return $response;
}



$queries = ["fiction", "fantasy", "science", "history", "romance"];

$count = 0;
$maxBooks = 1000;

foreach ($queries as $query) {

    $url = "https://openlibrary.org/search.json?q=" . urlencode($query);
    $response = fetchData($url);

    if (!$response)
        continue;

    $data = json_decode($response, true);

    if (!isset($data['docs']))
        continue;

    foreach ($data['docs'] as $book) {

        if ($count >= $maxBooks)
            break 2;

        $title = $book['title'] ?? null;
        $author = $book['author_name'][0] ?? null;

        if (!$title)
            continue;

        // cover image
        $cover = isset($book['cover_i'])
            ? "https://covers.openlibrary.org/b/id/" . $book['cover_i'] . "-L.jpg"
            : null;

        // OPTIONAL page count from OpenLibrary (often missing)
        $pages = $book['number_of_pages_median'] ?? null;


        // ✅ insert book
        $stmt = $pdo->prepare("
    INSERT IGNORE INTO books (title, author, cover_url, page_count)
    VALUES (?, ?, ?, ?)
");

        $stmt->execute([
            $title,
            $author,
            $cover,
            $pages
        ]);
        $bookId = $pdo->lastInsertId();

        // 🔥 STEP 1: Fetch genres from WORK endpoint




        $count++;

        // 🧠 small delay so API doesn’t freak out
        usleep(100000); // 0.1 sec
    }
}

echo "Inserted $count books!";





// DELETE FROM book_genres;
// DELETE FROM reviews;
// DELETE FROM books;
// DELETE FROM genres;

// ALTER TABLE book_genres AUTO_INCREMENT = 1;
// ALTER TABLE reviews AUTO_INCREMENT = 1;
// ALTER TABLE books AUTO_INCREMENT = 1;
// ALTER TABLE genres AUTO_INCREMENT = 1;
?>