let tagSet = new Set();
let allowedTags;
let returnedBooks;
document.addEventListener("DOMContentLoaded", () => {
    let form_btn = document.querySelector("#board-submit");

    let form = document.querySelector("#cr-board");


    fetch("../php/get_ids.php").then(response => response.json()).then(data => {
        allowedTags = data;
        console.log(allowedTags);

    });
    fetch("../php/get_boards.php").then(response => response.json()).then(data => {
        let selector = document.querySelector("#boards");
        for (board of data) {
            let op = document.createElement("option");
            op.setAttribute("value", board.id);
            op.textContent = board.name;
            selector.appendChild(op);
        }
    });


    let search = document.querySelector("#book-search");
    search.addEventListener("keydown", async (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            let book = search.value.trim().toLowerCase();
            books = await requestedBooks(book);
            console.log(books);
            loadBooks(books);

        }

    });
    search.addEventListener("keyup", async (e) => {
        if (e.key === "Backspace" && search.value == "") {

            let searchCaro = document.querySelector("#search-carousel");
            searchCaro.innerHTML = "";
            return;

        }
        let book = search.value.trim().toLowerCase();
        books = await requestedBooks(book);
        console.log(books);
        loadBooks(books);
        returnedBooks = books;
        console.log(returnedBooks);

    });
    let caro = document.querySelector("#search-carousel");
    caro.addEventListener("click", (e) => {


        if (e.target && (e.target.nodeName === "BUTTON" || e.target.nodeName === "I")) {
            e.preventDefault();
            let addedBooks = document.querySelector("#added-books");
            let book;
            for (check of returnedBooks) {
                console.log(check);
                if (check.id == e.target.dataset["id"]) {
                    addedBooks.appendChild(createAddCard(check));
                }
            }

        }
    });



    let tagInput = document.querySelector("#post-tags");

    tagInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            let val = tagInput.value.trim().toLowerCase();
            if (val !== "" && !tagSet.has(val)) {
                if (!checkTag(val)) {
                    alert("Tag not valid, try something else");
                    return;
                }
                addTag(getTagName(val));
                tagInput.value = "";


            }
        }

    });
    let colorBackground = document.querySelector("#color-sel");
    colorBackground.addEventListener("change", () => {
        let main = document.querySelector("#post-main");
        main.style.backgroundColor = colorBackground.value;
    });

    document.querySelector("#post").addEventListener("submit",saveBlogForm);

    // form.addEventListener("submit", (e) => {
    //     e.preventDefault();
    //     console.log("hit");
    //     let title = document.querySelector("#board-title");
    //     if (title.value === "") {
    //         alert("Title must be filled out");
    //         return;
    //     }
    //     let desc = document.querySelector("#b-descr");
    //     if(desc.value === ""){
    //         alert("Description must be filled out");
    //         return;
    //     }
    //     let tags = document.querySelectorAll(".pill");
    //     if (tags.length == 0) {
    //         alert("At least one tag needs to be selected");
    //         return;
    //     }

    //     form.submit();



    // });








});

function formatDoc(cmd, value = null) {
    document.execCommand(cmd, false, value);
    document.getElementById('editor').focus();
}
function saveBlogForm() {
    const rawHTML = document.getElementById('editor').innerHTML;
    document.getElementById('blogContent').value = rawHTML;
}
async function requestedBooks(info) {
    let resp = await fetch("../php/get_books_search.php?info=" + info);
    let books = await resp.json();
    return books;

}

function addTag(value) {
    tagSet.add(value);
    let div = document.querySelector("#tag-pills");
    let pill = document.createElement("div");
    let tag = document.createElement("span");
    tag.classList.add("pill");
    let del = document.createElement("button");
    let icon = document.createElement("i");
    icon.classList.add("bi");
    icon.classList.add("bi-x");
    del.classList.add("remove-btn");
    del.appendChild(icon);
    tag.textContent = value;
    tag.appendChild(del);
    pill.appendChild(tag);

    let input = document.createElement("input");
    input.setAttribute("type", "hidden");
    input.setAttribute("name", "tags[]");
    input.setAttribute("value", value);

    let tagId = document.createElement("input");
    tagId.setAttribute("type", "hidden");
    tagId.setAttribute("name", "tag_id[]");
    tagId.setAttribute("value", getTag(value));

    pill.appendChild(input);
    pill.appendChild(tagId);

    div.appendChild(pill);

    pill.querySelector(".remove-btn").addEventListener("click", (e) => {
        tagSet.delete(value);
        pill.remove();
    });

}
function checkTag(value) {
    return allowedTags.some(tag => tag.name.toLowerCase() === value);
}

function getTag(value) {

    let t = allowedTags.find(tag => tag.name.toLowerCase() === value.toLowerCase());
    return t.id;
}
function getTagName(value) {
    let name;
    allowedTags.forEach(tag => {
        if (tag.name.toLowerCase() == value) {
            name = tag.name;
        }
    });
    return name;
}

function loadBooks(books) {
    let searchCaro = document.querySelector("#search-carousel");
    searchCaro.innerHTML = "";
    books.forEach(book => {

        let card = document.createElement("div");
        card.classList.add("card");
        card.classList.add("caro-card");

        let content = document.createElement("div");
        content.classList.add("card-content");




        let img = document.createElement("img");
        if (book.cover_url == null) {
            img.setAttribute("src", "../images/default_image.jpg");
        } else {
            img.setAttribute("src", book.cover_url);
        }
        content.appendChild(img);

        let cont = document.createElement("div");
        cont.classList.add("card-cont");

        let text = document.createElement("div");
        text.classList.add("card-text");

        let title = document.createElement("p");
        title.classList.add("card-title");
        //title.classList.add("title2");
        title.textContent = book.title;
        let details = document.createElement("p");
        details.classList.add("card-details");
        //details.classList.add("title3");
        details.textContent = "By " + book.author;



        text.appendChild(title);
        text.appendChild(details);

        cont.appendChild(text);


        let add_btn = document.createElement("div");
        add_btn.classList.add("card-add-btn");

        let btn = document.createElement("button");
        btn.classList.add("add-btn");
        btn.setAttribute("data-id", book.id);
        let i = document.createElement("i");
        i.classList.add("bi");
        i.classList.add("bi-plus-circle-fill");
        i.setAttribute("data-id", book.id);
        btn.appendChild(i);
        add_btn.appendChild(btn);

        cont.appendChild(add_btn);

        content.appendChild(cont);


        card.appendChild(content);
        searchCaro.appendChild(card);

    });
}

function createAddCard(book) {
    let div = document.createElement('div');
    div.classList.add("add-card");
    let img = document.createElement("img")
    console.log(book);
    if (book.cover_url == null) {
        img.setAttribute("src", "../images/default_image.jpg");
    } else {
        img.setAttribute("src", book.cover_url);
    }
    img.classList.add("add-img");

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

    let del = document.createElement("button");
    let icon = document.createElement("i");
    icon.classList.add("bi");
    icon.classList.add("bi-x");
    del.classList.add("remove-btn");
    del.appendChild(icon);



    let input = document.createElement("input");
    input.setAttribute("type", "hidden");
    input.setAttribute("name", "books[]");
    input.setAttribute("value", book.id);
    div.appendChild(input);

    div.appendChild(img);
    div.appendChild(del);
    data.appendChild(title);
    data.appendChild(details);
    div.appendChild(data);
    div.querySelector(".remove-btn").addEventListener("click", (e) => {

        div.remove();
    });
    return div;
}