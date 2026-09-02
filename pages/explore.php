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
    <link href="../css/text-styles.css" rel="stylesheet">
    <link href="../css/explore.css" rel="stylesheet">
    <script src="../js/base.js"></script>
    <script src="../js/main-explore.js"></script>
    <script src="../js/explore-filters.js"></script>
    <title>The Book Blog Club</title>
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
</head>

<body>
    <header id="header">

        <div id="title">
            <a href="<?= BASE ?>">
                <img id="logo" src="../images/Book-Blog-Club-Logo-Transparent.png" alt="the book blog club logo">
            </a>
        </div>

        <nav id="nav-bar">
            <div>
                <ul>
                    <li class="nav-button"><a id="home" href="../">Home</a></li>
                    <li class="nav-button"><a id="explore" href="../index.php/explore">Browse</a></li>
                    <?php
                    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
                        echo '<li class="nav-button"><a id="user-posts" href="boards">My Boards</a></li>';
                    }

                    ?>
                </ul>
            </div>

            <div class="search">
                <form class="explore-search">
                    <input type="search" name="search" id="search-box">
                    <button type="submit">
                        <i class="bi bi-search"></i>

                    </button>
                </form>
            </div>

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
    <div id="exp">
        <aside id="filters">
            <h3>Filters</h3>
            <button id="filter-update">Update</button>
            <div id="filters-container">
                <div class="filter-container" id="filter-genres-container">
                    <div>
                        <p>Genres</p>
                        <a id="g-search"><i class="bi bi-search"></i></a>
                        <form class="hide" id="genre-search">
                            <input type="text" id="genre-search-text" name="genre-search">
                        </form>
                    </div>
                    
                    <div class="filter-buttons-container" id="filter-genres">
                        <a class="genre filter-btn">Romance</a>
                    </div>
                    <a id="see-more"></a>

                </div>
                <div class="hide filter-container" id="filter-tags">
                    <p>Tags</p>
                    <a class="tags filter-btn">Romance</a>
                </div>
            </div>
        </aside>
        <div id="main-exp">

            <ul id="exp-pages">
                <li>
                    <div class="exp-card">
                        <img src="../images/default_image.jpg" class="exp-img">
                        <div class="book-data"> </div>
                    </div>
                </li>
            </ul>



            <ul class="pagination">


            </ul>




        </div>
    </div>
</body>

</html>