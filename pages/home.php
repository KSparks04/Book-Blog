<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include_once("php/base.inc.php");

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Ahom&family=Nova+Square&display=swap"
        rel="stylesheet">
    <link href="css/base.css" rel="stylesheet">
    <link href="css/text-styles.css" rel="stylesheet">
    <link href="css/main-home.css" rel="stylesheet">
    <script src="js/main-home.js"></script>
    <title>The Book Blog Club</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
</head>

<body>
    <header id="header">

        

        <nav id="nav-bar">
            <div id="title">
            <a href="./">
                <img id="logo" src="images/Book-Blog-Club-Logo-Transparent.png" alt="the book blog club logo">
                <!-- <h1>The Book Blog Club</h1> -->
            </a>


        </div>
            <ul>
                <li class="nav-button"><a id="home" href="./">Home</a></li>
                <li class="nav-button"><a id="explore" href="index.php/explore">Browse</a></li>
                <?php
                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
                    echo '<li class="nav-button"><a id="user-posts" href="index.php/boards">My Boards</a></li>';
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
                </div>
                <!-- <a href="<?= BASE_URL ?>/profile">Profile</a> -->

            </div>

        </nav>
    </header>
    <div id="main-content">
        <div id="feature-box1">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="visual"
                viewBox="0 0 1000 600" width="900" height="600" version="1.1">

                <g fill="#486c4a">
                    <path
                        d="M0 -138.7L31.1 -42.9L132 -42.9L50.4 16.4L81.6 112.2L0 53L-81.6 112.2L-50.4 16.4L-132 -42.9L-31.1 -42.9Z"
                        transform="translate(560 60)" />
                    <path
                        d="M0 -61.3L13.8 -19L58.3 -19L22.3 7.2L36.1 49.6L0 23.4L-36.1 49.6L-22.3 7.2L-58.3 -19L-13.8 -19Z"
                        transform="translate(149 391)" />
                    <path
                        d="M0 -76.5L17.2 -23.6L72.7 -23.6L27.8 9L44.9 61.8L0 29.2L-44.9 61.8L-27.8 9L-72.7 -23.6L-17.2 -23.6Z"
                        transform="translate(525 493)" />
                    <path
                        d="M0 -113.3L25.4 -35L107.7 -35L41.1 13.4L66.6 91.6L0 43.3L-66.6 91.6L-41.1 13.4L-107.7 -35L-25.4 -35Z"
                        transform="translate(858 336)" />
                    <path
                        d="M0 -97.2L21.8 -30L92.5 -30L35.3 11.5L57.1 78.6L0 37.1L-57.1 78.6L-35.3 11.5L-92.5 -30L-21.8 -30Z"
                        transform="translate(74 77)" />
                </g>
            </svg>
            <div id="blog-box" class="f1">
                <h2>Start Sharing Your Thoughts</h2>
                <div id="blog-btns">
                    <a id="crt-board" href="index.php/create-board">Create a new board</a>
                    <a id="crt-post" href="index.php/create-post">Create a new post</a>
                </div>

            </div>



        </div>

        <div id="feature-box2">
            <div class="f2">
                <h2>Popular Books</h2>
                <div id="home-books" class="books-carousel">
                    <!--Register event listener for carousel-->
                    <div class="card caro-card card-book hide">
                        <div class="card-content">
                            <img src="images/default_image.jpg">
                            <p class="card-title">Book Example</p>
                            <p class="card-details">By Author</p>

                        </div>

                    </div>




                </div>
            </div>

            <div class="f2">
                <h2>Explore Popular Boards</h2>
                <div id="home-boards" class="boards-carousel">

                    <div class="card card-board caro-card">
                        <div class="card-content-board">
                            <div class="board-img-card"><img src="images/default_image.jpg"></div>
                            <div class="board-text">
                                <p class="card-title">Board Example</p>
                                <p class="card-details">By User</p>

                            </div>


                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

</body>

</html>