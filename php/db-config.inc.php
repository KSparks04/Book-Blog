<?php
$env = getenv('APP_ENV') ?: 'local';

define('DBHOST', getenv('DB_HOST') ?: 'localhost');
//echo DBHOST;
define('DBNAME', getenv('DB_NAME') ?: 'book_review_site');
//echo DBNAME;
define('DBUSER', getenv('DB_USER') ?: 'testuser');
define('DBPASS', getenv('DB_PASS') ?: 'mypassword');

define(
    'DBCONNSTRING',
    "mysql:host=" . DBHOST .
    ";dbname=" . DBNAME .
    ";charset=utf8mb4;"
);

?>
