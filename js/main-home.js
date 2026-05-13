document.addEventListener('DOMContentLoaded',()=>{

    fetch("get_books.php").then(results=> results.json()).then(data => {
        
        console.log(data);
        loadBookCards(data);
        });
});


function loadBookCards(books){
    let cardCarousel = document.querySelector(".books-carousel");
   
    books.forEach(book =>{
        //TODO: ADD RATING CHECK WITH population
        if(book.avg_rating >= 4){
             let card = document.createElement("div");
        card.classList.add("card");
        card.classList.add("caro-card");
       
        let content = document.createElement("div");
        content.classList.add("card-content");
        
        let img = document.createElement("img");
        if(book.cover_url == null){
            img.setAttribute("src","images/default_image.jpg");
        }else{
            img.setAttribute("src",book.cover_url);
        }
        
        let title = document.createElement("p");
        title.classList.add("card-title");
        title.textContent = book.title;
        let details = document.createElement("p");
        details.classList.add("card-details");
        details.textContent = "By "+book.author;
        


        content.appendChild(img);
        content.appendChild(title);
        content.appendChild(details);
        
        
        cardCarousel.appendChild(card);
        card.appendChild(content);
        }
       

    });
}

//  <div class="card caro-card">
//                         <div class="card-content">
//                             <img src="images/default_image.jpg">
//                             <p class="card-title">Book Example</p>
//                             <p class="card-details">By Author</p>
//                             <a>View Book</a>
//                         </div>

//                     </div>