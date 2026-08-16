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
    <link href="../css/text-styles.css" rel="stylesheet">
    <link href="../css/view-book.css" rel="stylesheet">
    <script src="../js/base.js"></script>
    <script src="../js/view-book.js"></script>
    <script src="../js/ratings.js"></script>
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
                </div>
            </div>

        </nav>
    </header>
    <main class="main-book-view">
        <div class="book-grid-container">
            <div class="book-cover">
                <img src="../images/default_image.jpg" alt="default_image">
            </div>
            <div class="book-content">
                <div class="book-title-section">
                    <h3 class="series title3 title3-italic"></h3>
                    <h1 class="book-title title1"></h1>
                    <h3 class="author title3"></h3>
                </div>

                <div>


                    <a href="#reader-reviews">
                        <span data-value="1">★</span>
                        <span data-value="2">★</span>
                        <span data-value="3">★</span>
                        <span data-value="4">★</span>
                        <span data-value="5">★</span>
                        Rating
                    </a>
                </div>
                <div class="book-descr-box">
                    <p class="book-descr line-clamp">Description -Overflow</p>
                    <button class="toggle-btn">Read more</button>
                </div>

                <div class="genre-box">
                    <h3>Genres</h3>
                    <div class="genre">
                        <ul class="genre-list">

                        </ul>

                    </div>
                </div>

            </div>
        </div>

        <div>
            <div>
                <h3>Reviews <i class="bi bi-stars"></i></h3>
                <div id="review-box2">
                    <div>
                        <p>Love it or Hate it?</p>
                        <a id="review">Write a Review</a>
                    </div>

                    <div id="star-rating">

                        <span data-value="1" class="icon-btn"><i class="bi bi-star-fill"></i></span>
                        <span data-value="2" class="icon-btn"><i class="bi bi-star-fill"></i></span>
                        <span data-value="3" class="icon-btn"><i class="bi bi-star-fill"></i></span>
                        <span data-value="4" class="icon-btn"><i class="bi bi-star-fill"></i></span>
                        <span data-value="5" class="icon-btn"><i class="bi bi-star-fill"></i></span>

                        <p>Rate this book</p>
                    </div>
                    <div id="update-review" class="hide">

                    </div>
                </div>
                <div id="update-review" class="hide">
                    <form id="review-form" method="post" action="post-review">
                        <div id="stars-place"></div>
                        <div class="editor-container">
                            <div class="toolbar">
                                <button type="button" onclick="formatDoc('bold')" title="Bold"><b>B</b></button>
                                <button type="button" onclick="formatDoc('italic')" title="Italic"><i>I</i></button>
                                <button type="button" onclick="formatDoc('underline')"
                                    title="Underline"><u>U</u></button>
                                <button type="button" onclick="formatDoc('insertUnorderedList')" title="Bullet List">•
                                    List</button>

                                <select onchange="formatDoc('formatBlock', this.value); this.selectedIndex=0;">
                                    <option value="" selected hidden disabled>Heading</option>
                                    <option value="H1">Heading 1</option>
                                    <option value="H2">Heading 2</option>
                                    <option value="P">Paragraph</option>
                                </select>
                            </div>

                            <div id="editor" contenteditable="true" placeholder="Start writing your blog post here...">
                            </div>
                        </div>

                        <input type="hidden" name="review_content" id="reviewContent">
                        <button id="review-submit">Submit</button>
                    </form>
                </div>
            </div>
            <hr>
            <div id="reader-reviews">
                <h3>Readers Reviews</h3>
                <div class="review-container">
                    <div class="filter-stars">
                        <a class="ratingLevel" data-rating="5">
                            <div class="modal-stars-single">
                                <div>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </div>
                                <span data-rating="5">5 Stars</span>
                            </div>

                        </a>
                        <a class="ratingLevel" data-rating="4">
                            <div class="modal-stars-single">
                                <div>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                </div>
                                <span data-rating="4">4 Stars</span>
                            </div>


                        </a>
                        <a class="ratingLevel" data-rating="3">
                            <div class="modal-stars-single">
                                <div>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                    <i class="bi bi-star"></i>
                                </div>
                                <span data-rating="3">3 Stars</span>

                            </div>

                        </a>
                        <a class="ratingLevel" data-rating="2">
                            <div class="modal-stars-single">
                                <div>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                    <i class="bi bi-star"></i>
                                    <i class="bi bi-star"></i>
                                </div>
                                <span data-rating="2">2 Stars</span>

                            </div>


                        </a>
                        <a class="ratingLevel" id="modal-star-1" data-rating="1">
                            <div class="modal-stars-single">
                                <div>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                    <i class="bi bi-star"></i>
                                    <i class="bi bi-star"></i>
                                    <i class="bi bi-star"></i>
                                </div>
                                <span data-rating="1">1 Star</span>

                            </div>

                        </a>




                    </div>
                    <div class="reviews-container">
                        <div class="reviews">
                            <div class="review-card">

                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>


        </div>

        </div>
    </main>
</body>

</html>