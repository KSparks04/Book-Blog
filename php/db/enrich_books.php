<?php
require_once("fetcher.php");
include_once("../db-config.inc.php");
$pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
set_time_limit(0); // 5 minutes
$lastRequestTime = microtime(true);
// get books missing metadata
$stmt = $pdo->prepare("
    SELECT id, title, author,work_key
    FROM books
    WHERE metadata_fetched = 0
    LIMIT 100;
");

$stmt->execute();

$books = $stmt->fetchAll();

foreach ($books as $book) {

    $bookId = $book['id'];

    $title = $book['title'];

    $author = $book['author'];
    $workKey = $book['work_key'];

    echo "<h1>Enriching: $title</h1>";





    $url = "https://openlibrary.org" . $workKey . ".json";


    $response = fetchData($url);

    if (!$response) {

        continue;
    }

    $data = json_decode($response, true);

    $subjects = $data['subjects'] ?? [];
    $lastRequestTime = microtime(true);
    $genres = [];
    $series = null;

    foreach ($subjects as $subject) {
        echo "<h3>$subject</h3>";
        if (str_contains($subject, 'series:')) {
            preg_match('/series:(.*?)(genre:|$)/', $subject, $m);
            $series = trim($m[1] ?? '');
        }

        preg_match_all('/genre:([^g]+)/', $subject, $genreMatches);
        $genreName = null;

        if (!empty($genreMatches[1])) {

            foreach ($genreMatches[1] as $g) {
                $genreName = trim($g);
                echo "<p>" . $g . "</p>";
            }
        }



        if (!$genreName) {
            $genreName = trim($subject);
        }


        // insert genre if not exists
        $stmt = $pdo->prepare("
        INSERT IGNORE INTO genres(name)
        VALUES(?)
    ");
        $stmt->execute([$genreName]);

        // get genre id
        $stmt = $pdo->prepare("
        SELECT id FROM genres WHERE name = ?
    ");
        $stmt->execute([$genreName]);

        $genreId = $stmt->fetchColumn();

        // link book ↔ genre
        $stmt = $pdo->prepare("
        INSERT IGNORE INTO book_genres(book_id, genre_id)
        VALUES(?, ?)
    ");
        $stmt->execute([$bookId, $genreId]);


        // update series (FIXED)
        $stmt = $pdo->prepare("
        UPDATE books
        SET series = ?
        WHERE work_key = ?
    ");

        $stmt->execute([$series, $workKey]);
        $stmt = $pdo->prepare("
        UPDATE books
        SET metadata_fetched = 1
        WHERE work_key = ?
    ");

        $stmt->execute([ $workKey]);
        

    }






    // avoid rate limits
    //usleep(00000); // 0.5 sec
    sleep(3);
}

echo "Finished enrichment";







?>