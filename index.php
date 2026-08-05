<?php
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$env = getenv('APP_ENV') ?: 'local';

$base = ($env === 'local') ? "/book-blog" : "";

$route = str_replace($base, "", $request);
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($basePath !== '') {
    $route = substr($request, strlen($basePath));
}

// echo $route;
$editRoute = str_replace("index.php/", "", $route);

//echo "<br>".$editRoute; // Output: /signup

switch ($editRoute) {
    case '/':
    case '/index':
    case '/index.php':
        require 'pages/home.php';
        break;
    case '/profile':
        require 'pages/profile.php';
        break;
    case '/explore':
        require 'pages/explore.php';
        break;
    case '/signup':
        require 'pages/signup.php';
        break;
    case '/login':
        require 'pages/login.php';
        break;
    case '/callback':
        require 'integrations/oauth2/callback.php';
        break;
    case '/userauth':
        require 'php/userauth.php';
        break;
    case '/view-book':
        require 'pages/view-book.php';
        break;
    case '/create-board':
        require 'pages/create-board.php';
        break;
    case '/create-post':
        require 'pages/create-post.php';
        break;
    case '/board-created':
        require 'pages/board-crt.php';
        break;
    case '/view-board':
        require 'pages/board.php';
        break;
    case '/view-post':
        require 'pages/post.php';
        break;
    case '/boards':
        require 'pages/view-board.php';
        break;
    case '/post-created':
        require 'pages/post-form.php';
        break;
    case '/post-review':
        require 'pages/review-posted.php';
        break;
    case '/success-signup':
        require 'pages/successful-signup.php';
        break;
    case '/success-login':
        require 'pages/successful-login.php';
        break;

    default:
        http_response_code(404);
        echo "Not found";
}

?>