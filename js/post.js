document.addEventListener("DOMContentLoaded", () => {
    let urlParams = new URLSearchParams(window.location.search);
    let postId = urlParams.get("id");
    console.log(postId);
    fetch("../php/get_post.php?id=" + postId).then(response => response.json()).then(data => {
        console.log(data);
        //let board = document.querySelector(".board");
        //console.log(data[0].background_image);
        //board.style.backgroundImage = "url(" + data[0].background_image + ")";
        //updateBoards(data);
        let postCont = document.querySelector(".post-bckgnd");
        postCont.style.backgroundColor = data[0].colour;
        createPost(data[0],postCont)

    });
});
async function createPost(posted, container) {
    console.log(posted);
    let postsTags = await fetch("../php/get_post_tags.php?post_id=" + posted.id);
    let tagged = await postsTags.json();
    let post = document.createElement("div");
    post.classList.add("post");
    let h3 = document.createElement("h3");
    h3.classList.add("post-title");
    h3.textContent = posted.title;
    post.appendChild(h3);

    let user = document.createElement("p");
    let username = await fetch("../php/get_post_user.php?id="+posted.user_id);
    let name = await username.json();
    console.log(name);
    user.textContent = name[0].username;
    post.appendChild(user);
    //NEED USER NAME GRAB FOR POST
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
    let postContent = document.createElement("div");
    postContent.innerHTML = posted.content;

    // let p = document.createElement("p");
    // p.textContent = posted.content;
    let extraOps = document.createElement("div");
    extraOps.classList.add("e-ops");
    let postRef = document.createElement("a");
    postRef.innerHTML = "<i class=\"bi bi-chat\"></i>Post a comment";



    content.appendChild(postContent);
    
    extraOps.appendChild(postRef);


    let mentions = document.createElement("div");
    let mentionsHeader = document.createElement("div");
    mentionsHeader.classList.add("m-header");
    let h4 = document.createElement("h4");
    h4.textContent = "Books mentioned";
    

    let carosel = document.createElement("div");
    carosel.classList.add("book-carousel");
   

    loadBooks(carosel, posted.id);


    mentionsHeader.appendChild(h4);
   
    mentions.appendChild(mentionsHeader);
    mentions.appendChild(carosel);

    // content.appendChild(mentions);

    post.appendChild(content);
    post.appendChild(extraOps);
    post.appendChild(mentions);
    container.appendChild(post);



}
async function loadBooks(cardCarousel, id) {
    let postsBooks = await fetch("../php/get_posts_book.php?post_id=" + id);
    let books = await postsBooks.json();

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
        card.appendChild(content);
        cardCarousel.appendChild(card);

    });
}