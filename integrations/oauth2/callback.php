<?php
session_start();
include_once("./php/db-config.inc.php");
include_once("./php/google-api.inc.php");
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

// 1. Validate state (CSRF protection)
if (!isset($_GET['state']) || $_GET['state'] !== $_SESSION['oauth_state']) {
    die("Invalid state parameter");
}

// 2. Get authorization code
if (!isset($_GET['code'])) {
    die("No authorization code returned");
}

$code = $_GET['code'];

// 3. Exchange code for access token
$token_url = "https://oauth2.googleapis.com/token";

$data = [
    "code" => $code,
    "client_id" => CLIENTID,
    "client_secret" => CLIENT_SECRET,
    "redirect_uri" => "http://127.0.0.1/book-blog/index.php?route=/callback",
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

// 4. Get user info from Google
$userInfoUrl = "https://www.googleapis.com/oauth2/v2/userinfo?access_token=" . $token['access_token'];
$userInfo = json_decode(file_get_contents($userInfoUrl), true);

// 5. Extract user data
$google_id = $userInfo['id'];
$email = $userInfo['email'];
$name = $userInfo['name'] ?? '';

// 6. Check if user exists in DB
$stmt = $pdo->prepare("SELECT id FROM users WHERE google_id = ?");
$stmt->execute([$google_id]);
$user = $stmt->fetch();

// 7. Create user if not exists
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

// 8. Log user in (session)
$_SESSION['user_id'] = $user_id;
$_SESSION['email'] = $email;
$_SESSION['logged_in'] = true;

// 9. Redirect to app
header("Location: /book-blog/index.php?route=/signup");
exit;