<?php
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$env = getenv('APP_ENV') ?: 'local';

$base = ($env === 'local') ? "/book-blog" : "";

$route = str_replace($base, "", $request);
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($basePath !== '') {
    $route = substr($request, strlen($basePath));
}

// echo __DIR__;
// echo '<br>';

// var_dump(file_exists(__DIR__ . '/php/signup.php'));
// exit;

switch ($route) {
    case '/':
    case '/index':
    case '/index.php':
        require 'pages/home.php';
        break;
    case '/signup':

        require 'pages/signup.php';
        break;

    case '/callback':
        require 'integrations/oauth2/callback.php';
        break;
    case '/userauth':
        require __DIR__ . '/php/signup.php';
        break;
    default:
        http_response_code(404);
        echo "Not found";
}

?>