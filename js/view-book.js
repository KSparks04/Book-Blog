document.addEventListener("DOMContentLoaded", () => {
    let urlParams = new URLSearchParams(window.location.search);
    let bookId = urlParams.get("id");
    console.log(bookId);
    fetch("../php/get_book.php?id=" + bookId).then(response => response.json()).then(data => {
        console.log(data);
        displayBook(data[0]);
    }).catch(error => {
        console.error("API Error:", error); // Added error catching to help you debug
    });
    fetch("../php/get_book_genre.php?id=" + bookId).then(response => response.json()).then(data => {
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
    document.querySelector("#review").addEventListener("click", reviewBox);
    let modal = document.getElementById("reviewModal");

    // Get the button that opens the modal
    let btn = document.querySelector("#reviewFilter");

    // Get the <span> element that closes the modal
    let span = document.getElementsByClassName("close")[0];


    btn.addEventListener("click", () => {
        modal.style.display = "block";
    });

    // When the user clicks on <span> (x), close the modal
    span.onclick = function () {
        modal.style.display = "none";
    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    let modalRatings = document.querySelector(".modal-content");
    modalRatings.addEventListener("click",(e)=>{
        if(e.target && e.target.nodeName == "A"){
            
            displayReviews(bookId,e.target.dataset["rating"]);
        }
    })

});
async function displayReviews(bookId, ratingLevel){
    let response = await fetch("../php/get_reviews.php?id="+bookId + "&level="+ratingLevel);
    let reviews  = await response.json();
    console.log(reviews);
    let panel = document.querySelector(".reviews");
    for(let review of reviews){
        console.log(review);
        let divR = document.createElement("div");
        divR.classList.add("review-card");
        let content = document.createElement("p");
        content.textContent = review.content;



        divR.appendChild(content);
        panel.appendChild(divR);

    }
}


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
