<?php
require_once("fetcher.php");
$title = urlencode($title);
$author = urlencode($author);

$url = "https://www.googleapis.com/books/v1/volumes?q=intitle:$title+inauthor:$author";

$response = fetchData($url);

$data = json_decode($response, true);

?>