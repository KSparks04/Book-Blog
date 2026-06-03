<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';


use Dotenv\Dotenv;

if (file_exists(__DIR__ . '/../../.env')) {
   
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
    $dotenv->load();
}

include_once("./php/db-config.inc.php");

$sslCa = __DIR__ . "/../certs/DigiCertGlobalRootCA.crt.pem";
$env = getenv('APP_ENV') ?: 'local';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

if ($env !== 'local') {
    $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
}
try {
    $pdo = new PDO(DBCONNSTRING, DBUSER, DBPASS, $options);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_GET['state']) || $_GET['state'] !== $_SESSION['oauth_state']) {
    die("Invalid state parameter");
}


if (!isset($_GET['code'])) {
    die("No authorization code returned");
}

$code = $_GET['code'];


$token_url = "https://oauth2.googleapis.com/token";
function getBaseUrl(): string
{
    
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || $_SERVER['SERVER_PORT'] == 443
        ? "https://"
        : "http://";

    
    $host = $_SERVER['HTTP_HOST'];

    
    $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

    $basePath = rtrim($scriptName, '/');

    return $protocol . $host . $basePath;
}
$uri = getBaseUrl();

$redirect_uri = "$uri/index.php/callback";

$data = [
    "code" => $code,
    "client_id" => $_ENV['CLIENTID'],
    "client_secret" =>$_ENV['CLIENT_SECRET'],
    "redirect_uri" =>$redirect_uri,
    "grant_type" => "authorization_code"
];

$options2 = [
    "http" => [
        "header" => "Content-type: application/x-www-form-urlencoded\r\n",
        "method" => "POST",
        "content" => http_build_query($data)
    ]
];

$context = stream_context_create($options2);
$response = file_get_contents($token_url, false, $context);
$token = json_decode($response, true);

if (!isset($token['access_token'])) {
    die("Failed to get access token");
}

$userInfoUrl = "https://www.googleapis.com/oauth2/v2/userinfo?access_token=" . $token['access_token'];
$userInfo = json_decode(file_get_contents($userInfoUrl), true);


$google_id = $userInfo['id'];
$email = $userInfo['email'];
$name = $userInfo['name'] ?? '';


$stmt = $pdo->prepare("SELECT id FROM users WHERE google_id = ?");
$stmt->execute([$google_id]);
$user = $stmt->fetch();


if (!$user) {
    $stmt = $pdo->prepare("
        INSERT INTO users (google_id, email, username, auth_provider)
        VALUES (?, ?, ?, 'google')
    ");
    $stmt->execute([$google_id, $email, $name]);

    $user_id = $pdo->lastInsertId();
} else {
    $user_id = $user['id'];
}


$_SESSION['user_id'] = $user_id;
$_SESSION['email'] = $email;
$_SESSION['logged_in'] = true;
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$basePath = rtrim($scriptName, '/');


header("Location: $basePath/index.php/signup");
exit;