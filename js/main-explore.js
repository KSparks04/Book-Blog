let books_array;
let pages;
let currentPage = 1;
document.addEventListener('DOMContentLoaded', () => {
    let result = fetch('php/get_books.php');
    result.then(data => { return data.json() }).then(results => { 
       
        console.log(results);
        loadExplore(results); 
        newPage(currentPage);  
       });
    
    document.querySelector(".pagination").addEventListener('click',(e)=>{
    if (e.target && e.target.nodeName === "A"){
        let num = e.target.id;
        newPage(num);

    }
});
});

function loadExplore(books) {
    let main = document.querySelector("#main-exp");
    pages = books.length / 25;
    books_array = books.map(createExploreCards);
    createPageNav();
}
function newPage(pageNum) {
    let page = document.querySelector("#exp-pages");
    page.innerHTML = '';
    let start = (pageNum-1) * 25;
    let limit = start+25;
    
    
    
    for (let i = start; i < limit; i++) {
        page.appendChild(books_array[i]);
    }
}
function createExploreCards(book) {
    let li = document.createElement('li');
    let div = document.createElement('div');
    div.classList.add("exp-card");
    let img = document.createElement("img")

    if (book.cover_url == null) {
        img.setAttribute("src", "images/default_image.jpg");
    } else {
        img.setAttribute("src", book.cover_url);
    }
    img.classList.add("exp-img");
    let title = document.createElement("p");
    title.classList.add("card-title");
    title.textContent = book.title;
    let details = document.createElement("p");
    details.classList.add("card-details");
    details.textContent = "By " + book.author;

    div.appendChild(img);
    div.appendChild(title);
    div.appendChild(details);
    li.appendChild(div);

    return li;
}

function createPageNav(){
    let nav = document.querySelector(".pagination");
    for(let i = 1;i <= pages; i++){
        let li = document.createElement("li");
        let link = document.createElement("a");
        link.setAttribute("id",i);
        link.setAttribute("href", "#top");
        link.textContent = i;
        li.appendChild(link);
        nav.appendChild(li);
    }

}