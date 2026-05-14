<?php
// 🔥 STEP 1: Fetch genres from WORK endpoint
$genres = [];
$workKey = $book['key'] ?? null;

if ($workKey) {
    $workUrl = "https://openlibrary.org" . $workKey . ".json";
    $workResponse = fetchData($workUrl);

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

    if (strlen($genreName) > 50)
        continue;

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

?>