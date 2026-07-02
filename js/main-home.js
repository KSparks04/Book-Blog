document.addEventListener('DOMContentLoaded', () => {

    fetch("php/get_books.php").then(results => results.json()).then(data => {

        //console.log(data);
        loadBookCards(data);
    });
    fetch("php/get_pop_boards.php").then(results => results.json()).then(boards => {
        loadBoards(boards);
    })

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
    }


        //}
    );
}
function loadBoards(boards) {
    let boardCarousel = document.querySelector(".boards-carousel");

    boards.forEach(board => {
        //TODO: ADD RATING CHECK WITH population
        // if (book.avg_rating >= 4) {
        let card = document.createElement("div");
        card.classList.add("card");
        card.classList.add("caro-card");
        card.classList.add("caro-board");

        let content = document.createElement("div");
        content.classList.add("card-content-board");

        let a = document.createElement("a");
        a.classList.add("board-link");
        a.setAttribute("href", "index.php/view-board?id=" + board.id);
        let imgDiv = document.createElement("div");
        imgDiv.classList.add("board-img-card");

        let img = document.createElement("img");
        if (board.background_image == null) {
            img.setAttribute("src", "../images/default_image.jpg");
        } else {
            let str = board.background_image.replace("../", "");
            img.setAttribute("src", str);
        }
        imgDiv.appendChild(img);

        let textDiv = document.createElement("div");
        textDiv.classList.add("board-text");
        console.log(board.user_id);
        fetch("php/get_board_users.php?id=" + board.user_id + "&board=" + board.id).then(res => res.json()).then(user => {
            console.log(user);
            let title = document.createElement("h2");
            title.classList.add("card-title");
            title.classList.add("title2");
            title.textContent = board.name;
            let details = document.createElement("h2");
            details.classList.add("card-details");
            details.classList.add("title3");
            details.textContent = "By " + user[0].username;

            textDiv.appendChild(title);
            textDiv.appendChild(details);



            a.appendChild(imgDiv);
            a.appendChild(textDiv);

            content.appendChild(a);

            boardCarousel.appendChild(card);
            card.appendChild(content);
        });


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