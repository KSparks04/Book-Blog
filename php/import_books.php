<?php
    include_once("db-config.inc.php");
    $pdo = new PDO(DBCONNSTRING, DBUSER,DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "fiction";
    $url = "https://openlibrary.org/search.json?q=" . urlencode($query);
function fetchData($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "User-Agent: Mozilla/5.0"
    ]);

    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

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
$response = fetchData($url);
if (!$response) {
    die("Failed to fetch data");
}
$data = json_decode($response, true);

$count = 0;

foreach ($data['docs'] as $book) {

    if ($count >= 20) break;

    $title = $book['title'] ?? null;
    $author = $book['author_name'][0] ?? null;
    $pages = $book['number_of_pages_median'] ?? null;

    if (!$title) continue;

    $cover = isset($book['cover_i']) 
        ? "https://covers.openlibrary.org/b/id/" . $book['cover_i'] . "-L.jpg"
        : null;

    // 🚫 Avoid duplicates
    $stmt = $pdo->prepare("SELECT id FROM books WHERE title = ? AND author = ?");
    $stmt->execute([$title, $author]);

    if ($stmt->fetch()) continue;

    // ✅ Insert book
    $stmt = $pdo->prepare("
        INSERT INTO books (title, author, cover_url, page_count)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$title, $author, $cover, $pages]);

    $bookId = $pdo->lastInsertId();

    // 🔥 STEP 1: Fetch genres from WORK endpoint
    $genres = [];
    $workKey = $book['key'] ?? null;

    if ($workKey) {
        $workUrl = "https://openlibrary.org" . $workKey . ".json";
        $workResponse = file_get_contents($workUrl);

        if ($workResponse) {
            $workData = json_decode($workResponse, true);

            if (isset($workData['subjects'])) {
                $genres = array_slice($workData['subjects'], 0, 3);
            }
        }
    }

    // 🔄 fallback if none found
    if (empty($genres)) {
        $genres = ["fiction"];
    }

    // 🔥 STEP 2: Insert genres
    foreach ($genres as $genreName) {

        $genreName = strtolower(trim($genreName));

        if (strlen($genreName) > 50) continue;

        // Insert genre
        $stmt = $pdo->prepare("INSERT IGNORE INTO genres (name) VALUES (?)");
        $stmt->execute([$genreName]);

        // Get id
        $stmt = $pdo->prepare("SELECT id FROM genres WHERE name = ?");
        $stmt->execute([$genreName]);
        $genreId = $stmt->fetchColumn();

        if ($genreId) {
            $stmt = $pdo->prepare("
                INSERT IGNORE INTO book_genres (book_id, genre_id)
                VALUES (?, ?)
            ");
            $stmt->execute([$bookId, $genreId]);
        }
    }

    $count++;
}



echo "Imported $count books successfully!";
// DELETE FROM book_genres;
// DELETE FROM reviews;
// DELETE FROM books;
// DELETE FROM genres;

// ALTER TABLE book_genres AUTO_INCREMENT = 1;
// ALTER TABLE reviews AUTO_INCREMENT = 1;
// ALTER TABLE books AUTO_INCREMENT = 1;
// ALTER TABLE genres AUTO_INCREMENT = 1;
?>

