<?php
    include_once("db-config.inc.php");
    $pdo = new PDO(DBCONNSTRING, DBUSER,DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT books.title, books.author,books.cover_url FROM books";
    // $sql = "SELECT books.title, books.author,books.cover_url, reviews.rating, reviews.content, AVG(reviews.rating) as avg_rating FROM books INNER JOIN reviews ON books.id = reviews.book_id";
    $results = $pdo->query($sql);
    $rows = $results->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
?>