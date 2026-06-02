
<?php
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$env = getenv('APP_ENV') ?: 'local';

$base = ($env === 'local') ? "/book-blog" : "";

$route = str_replace($base, "", $request);

$route = $_GET['route'] ?? '/';


switch ($route) {
    case '/':
        require 'pages/home.php';
        break;
    case '/index':
        require 'pages/home.php';
        break;
    case '/signup':
        
        require 'pages/signup.php';
        break;

    case '/callback':
        require 'integrations/oauth2/callback.php';
        break;
    case '/test':
        require '/php/test.php';
        break;

    default:
        http_response_code(404);
        echo "Not found";
}

?>
