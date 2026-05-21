<?php

define('DBHOST', getenv('DB_HOST') ?: 'localhost');
define('DBNAME', getenv('DB_NAME') ?: 'book_review_site');
define('DBUSER', getenv('DB_USER') ?: 'testuser');
define('DBPASS', getenv('DB_PASS') ?: 'mypassword');

define(
    'DBCONNSTRING',
    "mysql:host=" . DBHOST .
    ";dbname=" . DBNAME .
    ";charset=utf8mb4;sslmode=require"
);

?>