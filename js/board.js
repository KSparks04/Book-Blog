document.addEventListener("DOMContentLoaded", () => {
    let urlParams = new URLSearchParams(window.location.search);
    let boardId = urlParams.get("id");
    console.log(boardId);
    fetch("../php/get_board.php?id=" + boardId).then(response => response.json()).then(data => {
        console.log(data);
        let board = document.querySelector(".board");
        console.log(data[0].background_image);
        //board.style.backgroundImage = "url(" + data[0].background_image + ")";
        updateBoards(data);

    });
});


async function updateBoards(data) {


    //let board = document.querySelector(".board");
    //console.log(data[0].background_image);
    //board.style.backgroundImage = "url(" + data[0].background_image + ")";
    for (let boardInfo of data) {
        let postsResponse = await fetch("../php/get_posts.php?board_id=" + boardInfo.id);
        let posts = await postsResponse.json();

        let tagsResponse = await fetch("../php/get_board_tags.php?board_id=" + boardInfo.id);
        let tagsFetch = await tagsResponse.json();

        let board = document.createElement("div");
        board.classList.add("board");
        board.style.backgroundImage = "url(" + boardInfo.background_image + ")";

        let head = document.createElement("header");
        head.innerHTML = " <h2 class=\"board-title\">" + boardInfo.name + "</h2>";
        board.appendChild(head);

        let tags = document.createElement("div");
        tags.classList.add("board-tags");

        tagsFetch.forEach(tag => {
            let tg = document.createElement("a");
            tg.classList.add("tag");
            let sp = document.createElement("span");
            sp.textContent = tag.name;
            tg.appendChild(sp);
            tags.appendChild(tg);
        });
        board.appendChild(tags);



        for(let post of posts){
            let pst = await createPost(post)
            board.appendChild(pst);
        }


        
        document.querySelector(".page").appendChild(board);


        //Post 





    }
}

async function createPost(posted) {
    console.log(posted);
    let postsTags = await fetch("../php/get_post_tags.php?post_id=" + posted.id);
    let tagged = await postsTags.json();
    let post = document.createElement("div");
    post.classList.add("post");
    let h3 = document.createElement("h3");
    h3.classList.add("post-title");
    h3.textContent = posted.title;
    post.appendChild(h3);
    //TODO: DYNAMIC loading content and grabbing it


    let tags = document.createElement("div");
    tags.classList.add("tags")
   
    tagged.forEach(tag => {
        let tg = document.createElement("a");
        tg.classList.add("tag");
        let sp = document.createElement("span");
        sp.textContent = tag.name;
        tg.appendChild(sp);
        tags.appendChild(tg);
    });

    

   
    

    post.appendChild(tags);

    let content = document.createElement("div");
    content.classList.add("post-content");

    let p = document.createElement("p");
    p.textContent = posted.content;
    let postRef = document.createElement("a");
    postRef.innerHTML = "<i class=\"bi bi-chat\"></i>Post a comment";
    let readRef = document.createElement("a");
    readRef.textContent = "Read more";

    content.appendChild(p);
    content.appendChild(postRef);
    content.appendChild(readRef);

    let mentions = document.createElement("div");
    let h4 = document.createElement("h4");
    h4.textContent = "Books mentioned";
    let btn = document.createElement("button");
    btn.classList.add("mention-btn");
    btn.innerHTML = "<i class=\"bi bi-chevron-down\"></i>";

    let carosel = document.createElement("div");
    carosel.classList.add("book-carousel");
    //loadBook(books,carosel);

    mentions.appendChild(h4);
    mentions.appendChild(btn);
    mentions.appendChild(carosel);

    content.appendChild(mentions);

    post.appendChild(content);
    return post;



}

function loadBooks(books, cardCarousel) {


    books.forEach(book => {
        //TODO: ADD RATING CHECK WITH population
        // if (book.avg_rating >= 4) {
        let card = document.createElement("div");
        card.classList.add("card");
        card.classList.add("caro-card");

        let content = document.createElement("div");
        content.classList.add("card-content");

        let a = document.createElement("a");
        a.classList.add("book-link");
        a.setAttribute("href", "index.php/view-book?id=" + book.id);

        let img = document.createElement("img");
        if (book.cover_url == null) {
            img.setAttribute("src", "../images/default_image.jpg");
        } else {
            img.setAttribute("src", book.cover_url);
        }

        let title = document.createElement("h2");
        title.classList.add("card-title");
        title.classList.add("title2");
        title.textContent = book.title;
        let details = document.createElement("h2");
        details.classList.add("card-details");
        details.classList.add("title3");
        details.textContent = "By " + book.author;



        a.appendChild(img);
        a.appendChild(title);
        a.appendChild(details);
        content.appendChild(a);

        cardCarousel.appendChild(card);
        card.appendChild(content);
    });
}