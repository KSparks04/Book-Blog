let maxGenres = 10;
let filters = {
    genres: [],
    tags: [],
    author: "",
    rating: ""
}
document.addEventListener('DOMContentLoaded', () => {
    loadGenres();
    let genreSect = document.querySelector("#filter-genres");
    genreSect.addEventListener("click", (e) => {
        if (e.target && e.target.nodeName === "A") {
            if (!filters.genres.includes(e.target.dataset["filterId"])) {
                filters.genres.push(e.target.dataset["filterId"]);
            }

        }
    });
    document.querySelector("#filter-update").addEventListener("click", () => {
        const params = new URLSearchParams();

        params.append("author", filters.author);

        filters.genres.forEach(genre => {
            params.append("genres[]", genre);
        });

        const url = `${params.toString()}`;
        let results = fetch("../php/get_filters.php?"+url).then(resp =>{return resp.json()}).then(data =>{
            console.log(data);
        });
    });
    document.querySelector("#g-search").addEventListener("click",()=>{
        document.querySelector("#genre-search").classList.toggle("hide");
    })
    let genreSearch =document.querySelector("#genre-search-text");
    genreSearch.addEventListener("keyup",async (e)=>{
         if (e.key === "Backspace" && genreSearch.value == "") {
            loadGenres();
            
            return;

        }
        let genre = genreSearch.value.trim().toLowerCase();
        books = await loadSpecificGenres(genre);
    })
})
async function loadSpecificGenres(genre){
    let genreCont = document.querySelector("#filter-genres");
    genreCont.innerHTML = "";
    let genresResp = await fetch("../php/get_genre_specific.php?genre="+genre);
    let genres = await genresResp.json();
    console.log(genres);
    for(let g of genres){
        genreCont.appendChild(createGenreButton(g));
    }
}
async function loadGenres() {
    let genreCont = document.querySelector("#filter-genres");
    genreCont.innerHTML ="";
    let genresResp = await fetch("../php/get_genres.php");
    let genres = await genresResp.json();
    
    console.log(genres);


    let genresMore = document.querySelector("#see-more");
    genresMore.classList.add("see-more");
    genresMore.classList.add("hide");
    genresMore.textContent = "See more";
    genresMore.addEventListener("click", () => {
        genresMore.classList.toggle("hide");
        loadMoreGenres(genreCont, genres, genresMore);
    })

    for (let i = 1; i <= maxGenres; i++) {

        if (i === maxGenres) {
            genresMore.classList.toggle("hide");
            break;
        }
        let genre = genres.shift();
        genreCont.appendChild(createGenreButton(genre));


    }
    console.log(genres);
    

}
function createGenreButton(genre) {
    let aG = document.createElement("a");
    aG.setAttribute("data-filter-id", genre.id);
    aG.textContent = genre.name;
    aG.classList.add("genre");
    aG.classList.add("filter-btn");

    return aG;
}
function loadMoreGenres(genreCont, genres, genresMoreBtn) {
    for (let i = 1; i <= maxGenres; i++) {

        if (i === maxGenres) {
            genresMoreBtn.classList.toggle("hide");
            break;
        }
        let genre = genres.shift();
        genreCont.appendChild(createGenreButton(genre));


    }

}