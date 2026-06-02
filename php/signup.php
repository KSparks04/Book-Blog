<?php
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();


include_once("google-api.inc.php");
session_start();

$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;
$_SESSION['logged_in'] = false;

// $client_id = CLIENTID;
$client_id = $_ENV['CLIENTID'];


$redirect_uri = urlencode("http://127.0.0.1/book-blog/index.php?route=/callback");
$scope = urlencode("openid email profile");

header("Location: https://accounts.google.com/o/oauth2/v2/auth?" .
    "client_id=$client_id" .
    "&redirect_uri=$redirect_uri" .
    "&response_type=code" .
    "&scope=$scope" .
    "&state=$state"
);
exit;
