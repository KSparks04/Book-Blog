<?php


$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

// localhost: /book-blog
// production:  (empty or / depending on setup)
define('BASE', $scriptDir);
define('BASE_URL', $scriptDir."/index.php");

?>