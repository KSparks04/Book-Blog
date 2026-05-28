document.addEventListener('DOMContentLoaded', () => {

    fetch("php/get_books.php").then(results => results.json()).then(data => {

        //console.log(data);
        loadBookCards(data);
    });
});


function loadBookCards(books) {
    let cardCarousel = document.querySelector(".books-carousel");

    books.forEach(book => {
        //TODO: ADD RATING CHECK WITH population
        // if (book.avg_rating >= 4) {
        let card = document.createElement("div");
        card.classList.add("card");
        card.classList.add("caro-card");

        let content = document.createElement("div");
        content.classList.add("card-content");

        let a  = document.createElement("a");
        a.classList.add("book-link");
        a.setAttribute("href","view-book.html?id="+book.id);

        let img = document.createElement("img");
        if (book.cover_url == null) {
            img.setAttribute("src", "images/default_image.jpg");
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
    }


        //}
    );
}

//  <div class="card caro-card">
//                         <div class="card-content">
//                             <img src="images/default_image.jpg">
//                             <p class="card-title">Book Example</p>
//                             <p class="card-details">By Author</p>
//                             <a>View Book</a>
//                         </div>

//                     </div>