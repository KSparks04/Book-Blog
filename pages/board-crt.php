<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include_once("php/base.inc.php");


?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="../css/base.css" rel="stylesheet">
    <link href="../css/create-board.css" rel="stylesheet">
   
    <title>The Book Blog Club</title>
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
</head>

<body>
    <header id="header">

        <div id="title">
            <a href="../">
                <img id="logo" src="../images/Book-Blog-Club-Logo-Transparent.png" alt="the book blog club logo">
            </a>
        </div>
        <nav id="nav-bar">
            <ul>
                <li class="nav-button"><a id="home" href="../">Home</a></li>
                <li class="nav-button"><a id="explore" href="../index.php/explore">Browse</a></li>
                <?php 
                    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true){
                        echo '<li class="nav-button"><a id="user-posts" href="user-posts.html">My Posts</a></li>';
                    }
                
                ?>
            </ul>
            <div id="user-create">
                <a id="signup" href="<?= BASE_URL ?>/signup">Login</a>
            </div>

        </nav>
    </header>
    <?php
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
        header("Location:" . BASE_URL . "/signup");
    }

    ?>
    <div>
        <h3>Board Created</h3>
        <a>View Created Board</a>
        <a>Return to Home Page</a>
    </div>

</body>

</html>