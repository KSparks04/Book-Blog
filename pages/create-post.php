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
    <link href="../css/create-post.css" rel="stylesheet">
    <script src="../js/create-post.js"></script>
    <title>The Book Blog Club</title>
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
</head>

<body>
    <header id="header">

        <div id="title">
            <a href="index.html">
                <img id="logo" src="../images/Book-Blog-Club-Logo-Transparent.png" alt="the book blog club logo">
            </a>
        </div>
        </div>
        <nav id="nav-bar">
            <ul>
                <li class="nav-button"><a id="home" href="../">Home</a></li>
                <li class="nav-button"><a id="explore" href="../index.php/explore">Browse</a></li>
                <?php
                if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
                    echo '<li class="nav-button"><a id="user-posts" href="index.php/boards">My Boards</a></li>';
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
    <form method="post" action="post-created" id="post">
        
        <div id="post-page">

            <div id="post-main">
                <div id="main">
                    <div id="pst-title" class="post-base">
                        <label for="post-title">Title</label>
                        <input type="text" name="post-title" id="post-title">

                        <input type="color" id="color-sel" name="color-sel">
                    </div>

                    <div class="editor-container">
                        <div class="toolbar">
                            <button type="button" onclick="formatDoc('bold')" title="Bold"><b>B</b></button>
                            <button type="button" onclick="formatDoc('italic')" title="Italic"><i>I</i></button>
                            <button type="button" onclick="formatDoc('underline')" title="Underline"><u>U</u></button>
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

                    <input type="hidden" name="blog_content" id="blogContent">


                    <div id="book-mentioned">
                        <input type="text" id="book-search" name="book-search" placeholder="Books mentioned in post">
                        <div id="search-carousel">
                            <!-- <div class="card caro-card">
                            <div class="card-content">
                                <img src="../images/default_image.jpg">
                                <div class="card-cont">
                                    <div class="card-text">
                                        <p class="card-title">Book Example</p>
                                        <p class="card-details">By Author</p>

                                    </div>
                                    <div class="card-add-btn">
                                        <button class="add-btn">
                                            <i class="bi bi-plus-circle-fill"></i>

                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div> -->
                        </div>
                        <div id="added-books">
                            <!-- <div class="add-card">
                            <img src="../images/default_image.jpg" class="add-img">
                            <div class="book-data"> </div>
                        </div> -->
                        </div>
                    </div>
                </div>


            </div>
            <aside id="side-bar">
                <div id="submit"><button>Submit</button></div>
                <div id="post-setting">
                    <h4>Post Settings</h4>
                    <div id="tag-sel">
                        <label for="post-tags">Tags</label>
                        <input type="text" name="tags" id="post-tags">
                        <div id="tag-pills"></div>
                    </div>
                    <div id="board-sel">
                        <label for="boards">Posting to </label>
                        <select id="boards" name="boards-select">
                            <option value="0">New Board</option>
                        </select>

                    </div>


                </div>
            </aside>
        </div>


    </form>

</body>

</html>