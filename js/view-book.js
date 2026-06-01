document.addEventListener("DOMContentLoaded", () => {
    let urlParams = new URLSearchParams(window.location.search);
    let bookId = urlParams.get("id");
    console.log(bookId);
    fetch("php/get_book.php?id=" + bookId).then(response => response.json()).then(data => {
        console.log(data);
        displayBook(data[0]);
    }).catch(error => {
        console.error("API Error:", error); // Added error catching to help you debug
    });
    fetch("php/get_book_genre.php?id=" + bookId).then(response => response.json()).then(data => {
        console.log(data);
        displayGenres(data);
    }).catch(error => {
        console.error("API Error:", error); // Added error catching to help you debug
    });
    let readExpand = document.querySelector(".toggle-btn");
    readExpand.addEventListener("click", (e) => {
        if (e.target && e.target.nodeName == "BUTTON") {
            displayDescr(e.target);

        }
    });
    let stars = document.querySelector("#star-rating");
    stars.addEventListener("click", (e) => {
        if (e.target && e.target.nodeName === "SPAN") {
            console.log(e.target.dataset.value);
            highlightStars(e.target.dataset.value);
        }
    });
    stars.addEventListener("mouseover", (e) => {
        if (e.target && e.target.nodeName === "SPAN") {
            hoverStars(e.target.dataset.value);
        }
    });
    stars.addEventListener("mouseout", (e) => {
        if (e.target && e.target.nodeName === "SPAN") {
            removeStars(e.target.dataset.value);
        }
    });


});

function displayBook(bookData) {
    let cover = document.querySelector(".book-cover img");
    cover.setAttribute("src", bookData.cover_url);
    cover.setAttribute("alt", bookData.title);
    let content = document.querySelector(".book-title-section");

    if (bookData.series != null) {
        document.querySelector(".series").textContent = bookData.series;
    }

    document.querySelector(".book-title").textContent = bookData.title;
    document.querySelector(".author").textContent = bookData.author;
    document.querySelector(".book-descr").textContent = bookData.description;
    let list = document.querySelector(".genre-list");


}
function displayDescr(btn) {
    let descr = document.querySelector(".book-descr");

    descr.classList.toggle("line-clamp");
    if (!descr.classList.contains("line-clamp")) {
        btn.textContent = "Read Less";
    } else {
        btn.textContent = "Read More";
    }

}
function displayGenres(genres) {
    console.log(genres);
    let list = document.querySelector(".genre-list");
    genres.forEach((genre) => {
        let a = document.createElement("a");
        // ADD link to genre explore page
        let li = document.createElement("li");
        a.textContent = genre;
        li.appendChild(a);
        list.appendChild(li);

    });
}
function hoverStars(rating) {
    let stars = document.querySelectorAll('#star-rating span');
    stars.forEach(star => {
        //star.classList.remove('selected');
        star.classList.remove('hovered');
        if (parseInt(star.getAttribute('data-value')) <= rating) {
            star.classList.add('hovered');
        }
    });
}

function removeStars(rating) {
    let stars = document.querySelectorAll('#star-rating span');
    stars.forEach(star => {
        // star.classList.remove('selected');
        // star.classList.remove('hovered');
        if (parseInt(star.getAttribute('data-value')) >= rating && !star.classList.contains('selected')) {
            star.classList.remove('hovered');
            star.classList.remove('selected');
        }
    });
}
function highlightStars(rating) {
    let stars = document.querySelectorAll('#star-rating span');
    stars.forEach(star => {
        star.classList.remove('selected');
        star.classList.remove('hovered');
        if (parseInt(star.getAttribute('data-value')) <= rating) {
            star.classList.add('selected');
        }
    });
}