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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link href="../css/base.css" rel="stylesheet">
    <link href="../css/create-board.css" rel="stylesheet">
    <script src="../js/create-board.js"></script>
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
                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
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
        <h3>Create a Board</h3>
        <form method="post" id="cr-board" action="board-created" enctype="multipart/form-data">
            <div id="cr-b-title-create">
                <div id="b-main">
                    <div id="b-title">
                    <label for="board-title">Title</label>
                    <input type="text" name="board-title" id="board-title" placeholder="Create a title for your board">
                </div>
                <div id="b-descr">
                    <label for="description">Description</label>
                    <textarea name="description"></textarea>
                </div>
                </div>
                
                <div id="b-create-btn">
                    <button id="board-submit" type="submit">Create</button>
                </div>
            </div>
            <div id="background-img-b">
                <label for="board-img">Background Image</label>
                <div id="default-images">
                    <img class="default-img" data-img="1" src="../images/default-board-bckgnds/board-bkgnd1.jpg">
                    <img class="default-img" data-img="2" src="../images/default-board-bckgnds/board-bkgnd2.jpg">
                    <img class="default-img" data-img="3" src="../images/default-board-bckgnds/board-bkgnd3.jpg">
                    <img class="default-img" data-img="4" src="../images/default-board-bckgnds/board-bkgnd4.jpg">
                    <img class="default-img" data-img="5" src="../images/default-board-bckgnds/board-bkgnd5.jpg">
                    <img class="default-img" data-img="6" src="../images/default-board-bckgnds/board-bkgnd6.jpg">
                    <img class="default-img" data-img="7" src="../images/default-board-bckgnds/board-bkgnd7.jpg">
                    <img class="default-img" data-img="8" src="../images/default-board-bckgnds/board-bkgnd8.jpg">
                    <img class="default-img" data-img="9" src="../images/default-board-bckgnds/board-bkgnd9.jpg">




                </div>
                <div id="file-options">
                    <input type="file" name="board-img" id="board-img" accept="image/png, image/jpeg">
                    <button id="clear-file">Clear</button>
                </div>

            </div>
            <div id="b-tags">
                <label for="board-tags">Tags</label>
                <input type="text" name="tags" id="board-tags" placeholder="Pick some tags that match your board">
                <div id="tag-pills"></div>
            </div>
            <?php
            echo "<input type=hidden id=\"user-id\" value=" . $_SESSION['user_id'] . ">";
            ?>



        </form>
    </div>

</body>

</html>