document.addEventListener("DOMContentLoaded", () => {
    fetch("../php/get_boards.php").then(response => response.json()).then(data => {
        console.log(data);
        let board = document.querySelector(".board");
        console.log(data[0].background_image);
        //board.style.backgroundImage = "url(" + data[0].background_image + ")";
        document.querySelector(".page").innerHTML ="";
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
        let boardTitleContent = document.createElement("div");
        boardTitleContent.classList.add("board-title");

        let head = document.createElement("h2");
        head.textContent =  boardInfo.name;
        boardTitleContent.appendChild(head);

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
        boardTitleContent.appendChild(tags);
        board.appendChild(boardTitleContent);

        let postsDiv = document.createElement("div");
        postsDiv.classList.add("posts");

       

        console.log(posts.length);
        let postCreated = 1;
        let postsMore = document.createElement("a");
        postsMore.classList.add("posts-more");
        postsMore.textContent = "See more";
        for(let post of posts){
            if(postCreated >2){
                postsDiv.appendChild(postsMore);
                break;
            }
            postCreated++;
            let postCreate = await createPost(post);
            postsDiv.appendChild(postCreate);
        }
        
        board.appendChild(postsDiv);
        document.querySelector(".page").appendChild(board);


        //Post 





    }
}

async function createPost(posted) {
    let post = document.createElement("div");
    post.classList.add("post");
    let h3 = document.createElement("h3");
    h3.classList.add("post-title");
    h3.textContent = posted.title;
    //TODO: DYNAMIC loading content and grabbing it
    post.appendChild(h3);
     let postsTags = await fetch("../php/get_post_tags.php?post_id=" + posted.id);
    let tagged = await postsTags.json();

    let tags = document.createElement("div");
    tags.classList.add("tags");
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
    p.innerHTML = posted.content;
   
    let readRef = document.createElement("a");
    readRef.textContent = "Read more";

    content.appendChild(p);
    
    content.appendChild(readRef);

   

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