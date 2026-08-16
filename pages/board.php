<!DOCTYPE html>
<html lang="en">
<?php session_start();
include_once("php/base.inc.php");
include_once("php/db-config.inc.php");






?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../css/base.css" rel="stylesheet">
    <link href="../css/view-board.css" rel="stylesheet">
    <link href="../css/board.css" rel="stylesheet">
    <script src="../js/base.js"></script>
    <script src="../js/board.js"></script>
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
                    echo '<li class="nav-button"><a id="user-posts" href="boards">My Boards</a></li>';
                }

                ?>
            </ul>
            <div id="user-create">
                <?php
                if (!isset($_SESSION['email'])) {
                    echo "<a class=\"signup\" id=\"login-btn\"><i class=\"bi bi-person\"></i></a>";
                }
                ?>
                <div id="profile-wrapper">
                    <?php
                    if (isset($_SESSION["logged_in"]) && isset($_SESSION["email"])) {
                        echo "<a class = \"signup\" id=\"profile-view\"><i class=\"bi bi-person\"></i></a>";
                    }
                    ?>
                    <div id="profile-modal">
                        <div id="signout">
                            <p>Sign out</p>
                        </div>

                    </div>
                </div>


                <!-- <a id="signup" href="<?= BASE_URL ?>/signup"><i class="bi bi-person"></i></a> -->
                <div class="modal">
                    <div class="modal-container">
                        <span class="close">&times</span>
                        <!-- <div>
                            <h3>Create your account</h3>
                            <form id="sign-up" action="success-signup" method="post">
                                <label for="username">Displayed Username</label>
                                <input type="text" name="username" id="username">
                                <label for="email">Email Address</label>
                                <input type="text" name="email" id="email">
                                <p id="email-error"></p>
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password">
                                <p id="pwrd-error"></p>

                                <button type="submit" id="sign-up-btn">Sign up with email</button>
                            </form>
                            <a href="<?= BASE_URL ?>/userauth">
                                
                                <button id="google-signup">Continue with Google</button>
                            </a>
                        </div> -->
                        <div>
                            <?php


                            if (!isset($_SESSION['email'])) {
                                echo "
                            <div id=\"acct\">
                            <h3>Create your account</h3>
                            <form id=\"sign-up-form\" action=\"success-signup\" method=\"post\">
                                <label for=\"username\">Displayed Username</label>
                                <input type=\"text\" name=\"username\" id=\"username\">
                                <p id=\"user-error\"></p>
                                <label for=\"email\">Email Address</label>
                                <input type=\"text\" name=\"email\" id=\"email\">
                                <p id=\"email-error\"></p>
                                <label for=\"password\">Password</label>
                                <input type=\"password\" name=\"password\" id=\"password\">
                                <p id=\"pwrd-error\"></p>

                                <button type=\"submit\" id=\"sign-up-btn\">Sign up with email</button>
                            </form>
                            <p >Already have an account? <a id=\"acct-login\">Login</a></p>
                            </div>";

                            }

                            ?>
                            <a href="<?= BASE_URL ?>/userauth">

                                <button id="google-signup">Continue with Google</button>
                            </a>
                        </div>

                    </div>

        </nav>
    </header>

    <div class="page">
        <!-- <div class="board">
            <header>
                <h2 class="board-title">Board Title</h2>
            </header>
            <div class="board-tags">
                    <div class="tag">
                        <a>Example T</a>
                    </div>
                </div>
            <div class="post">
                <h3 class="post-title">Example Post Title</h3>
                -TODO: Date Posted
                <div class="tags">
                    <div class="tag">
                        <a>Example T</a>
                    </div>
                </div>
                <div class="post-content">
                    Overflow on it
                    <p>Example Post Information</p>
                    <a><i class="bi bi-chat"></i>Post a comment</a>
                    <a>Read more</a>
                    <div class="post-mentions">
                        <h4>Books mentioned</h4>
                        <button class="mention-btn">
                            <i class="bi bi-chevron-down"></i>

                        </button>
                        <div id="book-carousel">
                            <div class="card caro-card">
                                <div class="card-content">
                                    <img src="../images/default_image.jpg">
                                    <div class="card-cont">
                                        <div class="card-text">
                                            <p class="card-title">Book Example</p>
                                            <p class="card-details">By Author</p>

                                        </div>

                                    </div>




                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>-->
    </div>
</body>

</html>