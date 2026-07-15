document.addEventListener("DOMContentLoaded",()=>{
    let stars = document.querySelector("#star-rating");
    stars.addEventListener("click", (e) => {
        if (e.target && e.target.nodeName === "SPAN") {
            console.log(e.target.dataset.value);
            highlightStars(e.target.dataset.value);
        }
    });
    stars.addEventListener("mouseover", (e) => {
        if (e.target && e.target.nodeName === "SPAN" ) {
            hoverStars(e.target.dataset.value);
        }
    });
    stars.addEventListener("mouseout", (e) => {
        if (e.target && e.target.nodeName === "SPAN") {
            removeStars(e.target.dataset.value);
        }
    });
    stars.addEventListener("dblclick",(e)=>{
        if (e.target && e.target.nodeName === "SPAN") {
            removeStars(e.target.dataset.value);
        }
    });
    document.querySelector("#review-submit").addEventListener("submit",saveReviewForm);
});

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
         star.classList.remove('hovered');
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
function formatDoc(cmd, value = null) {
    document.execCommand(cmd, false, value);
    document.getElementById('editor').focus();
}
function saveReviewForm() {
    const rawHTML = document.getElementById('editor').innerHTML;
    document.getElementById('reviewContent').value = rawHTML;
}
function reviewBox(){
    let stars = document.querySelectorAll('#star-rating span');
     let urlParams = new URLSearchParams(window.location.search);
    let bookId = urlParams.get("id");
    let reviewBox = document.querySelector("#review-box2");
    reviewBox.innerHTML = "";
    let updateReview = document.querySelector("#update-review");
    updateReview.classList.remove('hide');
    let rating = 1;
    stars.forEach(star => {
        if( star.classList.contains('selected') && star.getAttribute('data-value') > rating){
            rating = star.getAttribute('data-value');
        }
        document.querySelector("#stars-place").appendChild(star);
    });
    let ratingInput =document.createElement("input");
    ratingInput.setAttribute("type","hidden");
    ratingInput.setAttribute("name","rating");
    ratingInput.setAttribute("value",rating);
    let iDInput =document.createElement("input");
    iDInput.setAttribute("type","hidden");
    iDInput.setAttribute("name","book-id");
    iDInput.setAttribute("value",bookId);
    let form = document.querySelector("#review-form");

form.appendChild(ratingInput);
form.appendChild(iDInput);

    
   
}