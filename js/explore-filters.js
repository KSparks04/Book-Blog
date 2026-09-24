let maxGenres = 10;
let filters = {
    genres: [],
    tags: [],
    author: "",
    rating: "",
    format: "books"
}
document.addEventListener('DOMContentLoaded', () => {
    loadGenres();
    let genreSect = document.querySelector("#filter-genres");
    genreSect.addEventListener("click", (e) => {
        if (e.target && e.target.nodeName === "A") {
            if (!filters.genres.includes(e.target.dataset["filterId"])) {
                filters.genres.push(e.target.dataset["filterId"]);
                e.target.classList.toggle("filter-btn-select");
            }

        }
    });
    let tagSect = document.querySelector("#filter-tag");
    tagSect.addEventListener("click", (e) => {
        if (e.target && e.target.nodeName === "A") {
            if (!filters.tags.includes(e.target.dataset["filterId"])) {
                filters.tags.push(e.target.dataset["filterId"]);
                e.target.classList.toggle("filter-btn-select");
            }

        }
    });
    document.querySelector("#filter-update").addEventListener("click", () => {
        const params = new URLSearchParams();

        params.append("author", filters.author);

        filters.genres.forEach(genre => {
            params.append("genres[]", genre);
        });
        params.append("format", filters.format);

        const url = `${params.toString()}`;
        let results = fetch("../php/get_filters.php?" + url).then(resp => { return resp.json() }).then(async(data) => {
            console.log(data);
            if (filters.format === "books") {
                loadExplore(data);
                newPage(currentPage);
            }else if (filters.format === "posts"){
               await loadExplorePosts(data);
                newPagePost(currentPage);
            }

        });
    });
    document.querySelector("#g-search").addEventListener("click", () => {
        document.querySelector("#genre-search").classList.toggle("hide");
    })
    let genreSearch = document.querySelector("#genre-search-text");
    genreSearch.addEventListener("keyup", async (e) => {
        if (e.key === "Backspace" && genreSearch.value == "") {
            loadGenres();

            return;

        }
        let genre = genreSearch.value.trim().toLowerCase();
        books = await loadSpecificGenres(genre);
    });
    let formatSel = document.querySelector("#format-select");
    formatSel.addEventListener("click", (e) => {
        if (e.target && e.target.nodeName === "INPUT") {
            filters.format = e.target.value;
            let tagContainer = document.querySelector("#filter-tags");

            if (e.target.value == "posts" || e.target.value == "blogs") {
                tagContainer.classList.remove("hide");
                populateTags();
            } else {
                tagContainer.classList.add("hide");
            }
        }
    })
})
async function loadSpecificGenres(genre) {
    let genreCont = document.querySelector("#filter-genres");
    genreCont.innerHTML = "";
    let genresResp = await fetch("../php/get_genre_specific.php?genre=" + genre);
    let genres = await genresResp.json();
    console.log(genres);
    for (let g of genres) {
        genreCont.appendChild(createGenreButton(g));
    }
}
async function loadGenres() {
    let genreCont = document.querySelector("#filter-genres");
    genreCont.innerHTML = "";
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
    let x = document.createElement("a");
    x.innerHTML = "&times";
    x.classList.add("close");
    // x.classList.add("hide");
    aG.addEventListener("click", () => {


        x.addEventListener("click", () => {
            let index = filters.genres.indexOf(genre.id);
            filters.genres.splice(index, 1);
            aG.classList.toggle("filter-btn-select");
            x.classList.toggle("hide");
        });
        aG.appendChild(x);
    })

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
function populateTags() {
    let tagContainer = document.querySelector("#filter-tag");
    fetch("../php/get_tags.php").then(resp => resp.json()).then(tags => {
        let tagsMore = document.querySelector("#see-more-tags");
        tagsMore.classList.add("see-more");
        tagsMore.classList.add("hide");
        tagsMore.textContent = "See more";
        tagsMore.addEventListener("click", () => {
            tagsMore.classList.toggle("hide");
            loadMoreTags(tagContainer, tags, tagsMore);
        })

        for (let i = 1; i <= maxGenres; i++) {

            if (i === maxGenres) {
                tagsMore.classList.toggle("hide");
                break;
            }
            let tag = tags.shift();
            tagContainer.appendChild(createTagButton(tag));


        }
        console.log(tags);
    })




}
function createTagButton(tag) {
    let aT = document.createElement("a");
    aT.setAttribute("data-filter-id", tag.id);
    aT.textContent = tag.name;
    aT.classList.add("tag");
    aT.classList.add("filter-btn");
    let x = document.createElement("a");
    x.innerHTML = "&times";
    x.classList.add("close");
    // x.classList.add("hide");
    aT.addEventListener("click", () => {


        x.addEventListener("click", () => {
            let index = filters.tags.indexOf(tag.id);
            filters.tags.splice(index, 1);
            aT.classList.toggle("filter-btn-select");
            x.classList.toggle("hide");
        });
        aT.appendChild(x);
    })

    return aT;
}
function loadMoreTags(tagCont, tags, tagsMoreBtn) {
    for (let i = 1; i <= maxGenres; i++) {

        if (i === maxGenres) {
            tagsMoreBtn.classList.toggle("hide");
            break;
        }
        let tag = tags.shift();
        tagCont.appendChild(createTagButton(tag));


    }

}