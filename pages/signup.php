<!DOCTYPE html>
<html lang="en">
<?php
include_once("php/base.inc.php");

?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="css/base.css" rel="stylesheet">
    <script src="js/signup-login.js"></script>
    <title>The Book Blog Club</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
</head>

<body>
    <header id="header">

        <div id="title">
            <a href="index.php"> 
               <img id="logo" src="images/Book Blog  Club Logo Transparent.png" alt="the book blog club logo">
            </a>
        </div>
        <nav id="nav-bar">
            <ul>
                <li class="nav-button"><a id="home" href="index.php">Home</a></li>
                <li class="nav-button"><a id="explore" href="explore.html">Browse</a></li>
                <li class="nav-button hide"><a id="user-posts" href="user-posts.html">My Posts</a></li>
            </ul>
            <div id="user-create">
                <a id="signup" class="hide">Sign Up</a>
                <a id="login" class="hide">Login</a>
            </div>

        </nav>
    </header>
    <main>
        <div>
            <a href="<?= BASE_URL?>/userauth">
                <button type="submit" id="google-signup">Sign up with Google</button>
            </a>
            <?php

                session_start();
                
                if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true){
                    
                    if(!isset($_SESSION['email'])){
                    echo "failure - for now";
                }
                echo $_SESSION['email'];

                }
                
            ?>
            
        </div>
    </main>
</body>

</html>