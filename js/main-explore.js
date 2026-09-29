let books_array;
let searchTimeout;
let currentPage = 1;
let previous;
let posts_array;
let bookAmount = 28000000;
let pagesArray = [];
document.addEventListener('DOMContentLoaded', () => {

    // fetchNewPage(currentPage);
    // createPageNav(bookAmount,currentPage);
    document.querySelector(".pagination").addEventListener('click', (e) => {
        if (e.target && e.target.nodeName === "A") {
            let num = e.target.id;
            fetchNewPage(num);

        }

    });
    let search = document.querySelector("#search-box");
    let enterPressed = false;
    search.addEventListener("keydown", async (e) => {
        // console.log("Key pressed on element:", document.activeElement);


    });
    search.addEventListener("input", async (e) => {

        if (e.key === "Enter") {
            e.preventDefault();
            enterPressed = true;
            // let book = search.value.trim().toLowerCase();
            // fetchNewSearchPage(book, 1);
            clearTimeout(searchTimeout);

            searchTimeout = setTimeout(() => {
                let book = search.value.trim().toLowerCase();
                fetchNewSearchPage(book, 1);

            }, 300);

        }
        // console.log("Key pressed on element:", document.activeElement);
        if (e.key === "Backspace" && search.value == "") {

            // fetchNewPage(1);
            // createPageNav(bookAmount, 1);

            return;

        }

        clearTimeout(searchTimeout);

        searchTimeout = setTimeout(() => {
            let book = search.value.trim().toLowerCase();
            fetchNewSearchPage(book, 1);

        }, 300);

    });



});

function fetchNewPage(pageNum) {
    let result = fetch('../php/get_books_explore.php?page=' + pageNum);
    result.then(data => { return data.json() }).then(results => {

        console.log(results);

        loadExplore(results);


    });
}
function fetchNewSearchPage(info, pageNum) {
    let result = fetch("../php/get_books_search.php?info=" + info + "&page=" + pageNum);

    result.then(data => { return data.json() }).then(results => {

        console.log(results);

        loadExplore(results);
        createPageNavExplore(results.length, 1);


    });

}
function pageCountBooks() {
    //  let result = fetch('../php/get_book_count.php');
    //     result.then(data => { return data.json() }).then(results => {

    //         console.log(results);
    //         console.log(results[0].amount);
    //         createPageNav(results[0].amount);
    //     });


}
function pageCountPosts() {
    let result = fetch('../php/get_post_count.php');
    result.then(data => { return data.json() }).then(results => {

        console.log(results);
        createPageNav(results);
    });

}
async function requestedBooks(info) {
    let resp = await fetch("../php/get_books_search.php?info=" + info);
    let books = await resp.json();
    return books;

}
function loadExplore(books) {

    books_array = books.map(createExploreCards);
    newPage();

}
async function loadExplorePosts(posts) {

    let main = document.querySelector("#main-exp");

    pages = Math.ceil(posts.length / 25);

    console.log(pages);

    posts_array = await Promise.all(
        posts.map(createExplorePostCards)
    );

    createPageNav();
}
function newPage() {
    let page = document.querySelector("#exp-pages");
    page.innerHTML = '';


    console.log(books_array);

    for (let i = 0; i < 25; i++) {

        page.appendChild(books_array[i]);
    }
}
function newPagePost(pageNum) {
    let page = document.querySelector("#exp-pages");
    page.innerHTML = '';
    let start = (pageNum - 1) * 25;
    let limit = start + 25;

    console.log(posts_array);

    for (let i = start; i < limit; i++) {
        if (i >= posts_array.length) {
            break;
        }
        page.appendChild(posts_array[i]);
    }
}
function createExploreCards(book) {
    let li = document.createElement('li');
    let div = document.createElement('div');
    div.classList.add("exp-card");
    let img = document.createElement("img")

    if (book.cover_url == null) {
        img.setAttribute("src", "../images/default_image.jpg");
    } else {
        img.setAttribute("src", book.cover_url);
    }
    img.classList.add("exp-img");

    let data = document.createElement('div');
    data.classList.add("book-data");

    let title = document.createElement("h2");
    title.classList.add("card-title");
    title.classList.add("title2-exp");
    title.textContent = book.title;
    let details = document.createElement("h2");
    details.classList.add("card-details");
    details.classList.add("title3-exp");
    details.textContent = "By " + book.author;

    div.appendChild(img);
    data.appendChild(title);
    data.appendChild(details);
    div.appendChild(data);
    let a = document.createElement("a");
    if(book.id){

    a.setAttribute("href", "../index.php/view-book?id=" + book.id+"&key="+book.work_key);
    }else{
        
    a.setAttribute("href", "../index.php/view-book?id=" +"&key="+book.work_key);
    }
    a.appendChild(div);
    li.appendChild(a);

    return li;
}
async function createExplorePostCards(post) {
    let li = document.createElement('li');
    let div = document.createElement('div');
    div.classList.add("exp-card");
    let img = document.createElement("img")

    img.setAttribute("src", "../images/default_image.jpg");
    // } else {
    //     img.setAttribute("src", book.cover_url);
    // }
    img.classList.add("exp-img");

    let data = document.createElement('div');
    data.classList.add("book-data");

    let title = document.createElement("h2");
    title.classList.add("card-title");
    title.classList.add("title2-exp");
    title.textContent = post.title;
    let details = document.createElement("h2");
    details.classList.add("card-details");
    details.classList.add("title3-exp");
    let userComment = await fetch("../php/get_post_user.php?id=" + post.user_id);
    let resp = await userComment.json();

    details.textContent = resp[0].username;

    div.appendChild(img);
    data.appendChild(title);
    data.appendChild(details);
    div.appendChild(data);
    let a = document.createElement("a");
    a.setAttribute("href", "../index.php/view-post?id=" + post.id);
    a.appendChild(div);
    li.appendChild(a);

    return li;
}

function createPageNav(pages, currentPage) {
    let nav = document.querySelector(".pagination");
    nav.innerHTML = "";
    let pagesAmount = Math.ceil((pages / 25));
    let previousBtn = document.createElement("a");
    previousBtn.textContent = "Previous";
    previousBtn.setAttribute("href", "#exp");
    previousBtn.addEventListener("click", () => {
        if (currentPage == 1) {
            return;
        }
        currentPage--;
        fetchNewPage(currentPage);
        createPageNav(pages, currentPage);
        return;

    })
    nav.appendChild(previousBtn);

    let currentAmt = document.createElement("p");
    currentAmt.textContent = currentPage + "/" + pagesAmount;
    nav.appendChild(currentAmt);

    let nextBtn = document.createElement("a");
    nextBtn.textContent = "Next";
    nextBtn.setAttribute("href", "#exp");
    nextBtn.addEventListener("click", () => {
        if (currentPage == pagesAmount) {
            return;
        }
        currentPage++;
        fetchNewPage(currentPage);
        createPageNav(pages, currentPage);
        return;
    })
    nav.appendChild(nextBtn);



}
function createPageNavExplore(pages, currentPage) {
    let nav = document.querySelector(".pagination");
    nav.innerHTML = "";
    let pagesAmount = Math.ceil((pages / 25));
    let previousBtn = document.createElement("a");
    previousBtn.textContent = "Previous";
    previousBtn.setAttribute("href", "#exp");
    previousBtn.addEventListener("click", () => {
        if (currentPage == 1) {
            return;
        }
        currentPage--;
        fetchNewSearchPage(currentPage);
        createPageNavExplore(pages, currentPage);
        return;

    })
    nav.appendChild(previousBtn);

    let currentAmt = document.createElement("p");
    currentAmt.textContent = currentPage + "/" + pagesAmount;
    nav.appendChild(currentAmt);

    let nextBtn = document.createElement("a");
    nextBtn.textContent = "Next";
    nextBtn.setAttribute("href", "#exp");
    nextBtn.addEventListener("click", () => {
        if (currentPage == pagesAmount) {
            return;
        }
        currentPage++;
        fetchNewSearchPage(currentPage);
        createPageNavExplore(pages, currentPage);
        return;
    })
    nav.appendChild(nextBtn);



}

