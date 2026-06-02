<?php
include_once 'base.inc.php';
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
session_start();

$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;
$_SESSION['logged_in'] = false;

// $client_id = CLIENTID;
$client_id = $_ENV['CLIENTID'];
function getBaseUrl(): string
{
    // Detect protocol (http vs https)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || $_SERVER['SERVER_PORT'] == 443
        ? "https://"
        : "http://";

    // Host (127.0.0.1, localhost, domain, azure site, etc.)
    $host = $_SERVER['HTTP_HOST'];

    // If your project is in a subfolder like /book-blog
    $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

    // Clean up trailing slash unless it's root
    $basePath = rtrim($scriptName, '/');

    return $protocol . $host . $basePath;
}
$uri = getBaseUrl();

$redirect_uri = urlencode("$uri/index.php/callback");
$scope = urlencode("openid email profile");

header("Location: https://accounts.google.com/o/oauth2/v2/auth?" .
    "client_id=$client_id" .
    "&redirect_uri=$redirect_uri" .
    "&response_type=code" .
    "&scope=$scope" .
    "&state=$state"
);
exit;
?>