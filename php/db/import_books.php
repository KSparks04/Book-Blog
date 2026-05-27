<?php
include_once("../db-config.inc.php");
require_once("openlibraryclient.php");
$pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
set_time_limit(0); // 5 minutes

$client = new OpenLibraryClient();




$queries = ["fiction", "fantasy", "science", "history", "romance", "science fanatasy", "LitRPG"];
// $queries = ["LitRPG"];
$count = 0;
$maxBooks = 250;
$maxPage = 10;

foreach ($queries as $query) {
    for ($page = 1; $page <= $maxPage; $page++) {


        $data = $client->searchBooks($query, $page);

        if (!isset($data['docs']))
            continue;


        foreach ($data['docs'] as $book) {

            if ($count >= $maxBooks)
                break 2;

            $title = $book['title'] ?? null;
            $author = $book['author_name'][0] ?? null;
            if (!$title)
                continue;

            $workKey = $book['key'] ?? null;
            // "/works/OL110971W"

            $editionKey = $book['cover_edition_key'] ?? null;
            if ($workKey) {


                $workData = $client->getWork($workKey);

                // genres / subjects
                $subjects = $workData['subjects'] ?? [];

                // description can be string OR object
                $description = null;

                if (isset($workData['description'])) {

                    if (is_array($workData['description'])) {
                        $description = $workData['description']['value'] ?? null;
                    } else {
                        $description = $workData['description'];
                    }
                }
            }

            $isbn13 = null;
            $isbn10 = null;

            if ($editionKey) {

                $editionData = $client->getEdition($editionKey);

                $isbn13 = $editionData['isbn_13'][0] ?? null;
                $isbn10 = $editionData['isbn_10'][0] ?? null;
            }
            

            


            // cover image
            $cover = isset($book['cover_i'])
                ? "https://covers.openlibrary.org/b/id/" . $book['cover_i'] . "-L.jpg"
                : null;

            // OPTIONAL page count from OpenLibrary (often missing)
            $pages = $book['number_of_pages_median'] ?? null;


            // ✅ insert book
            $stmt = $pdo->prepare("
    INSERT IGNORE INTO books (title, author, cover_url, page_count, isbn_10,isbn_13, description,work_key)
    VALUES (?, ?, ?, ?, ?,?,?,?)
");

            $stmt->execute([
                $title,
                $author,
                $cover,
                $pages,
                $isbn10,
                $isbn13,
                $description,
                $workKey
            ]);
            // fetch existing/new book id
            $stmt = $pdo->prepare("
    SELECT id FROM books
    WHERE title = ? AND author = ?
");

            $stmt->execute([$title, $author]);

            $bookId = $stmt->fetchColumn();




            $count++;

            // 🧠 small delay so API doesn’t freak out
            usleep(1500000); // 0.1 sec
        }
        $count = 0;

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
