<?php
    include_once("db-config.inc.php");
    $pdo = new PDO(DBCONNSTRING, DBUSER,DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM books";
    $results = $pdo->query($sql);
    $rows = $results->fetchAll();
    echo json_encode($rows);
?>