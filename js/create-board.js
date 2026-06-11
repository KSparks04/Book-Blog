let tagSet = new Set();
let newFile = false;
let defaultSel = false;
document.addEventListener("DOMContentLoaded", () => {
    let form_btn = document.querySelector("#board-submit");
    let file_up = document.querySelector("#board-img");
    let form = document.querySelector("#cr-board");
    let default_imgs = document.querySelectorAll(".default-img");
    file_up.addEventListener("change", (e) => {
        let file = e.target.files[0];
        console.log(file);
        if (!file) return;
        let size = 2 * 1024 * 1024;
        if (file.size > size) {
            form_btn.disabled = true;
            return;
        }
        newFile = true;
        defaultSel = false;
        clearDefault(default_imgs);




    });
    let tagInput = document.querySelector("#board-tags");

    tagInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            let val = tagInput.value.trim().toLowerCase();
            if (val !== "" && !tagSet.has(val)) {
                addTag(val);
                tagInput.value = "";


            }
        }

    });



    form.addEventListener("submit", (e) => {
        e.preventDefault();
        console.log("hit");
        let title = document.querySelector("#board-title");
        if (title.value === "") {
            alert("Title must be filled out");
            return;
        }
        let tags = document.querySelectorAll(".pill");
        if(tags.length == 0){
            alert("At least one tag needs to be selected");
            return;
        }

        if (newFile) {
            let randNum = document.createElement("input");
            randNum.setAttribute("type", "hidden");
            randNum.setAttribute("name", "file_num");
            randNum.setAttribute("value", randomNumber());
            form.appendChild(randNum);
            //alert(randNum);
        }
        if(!newFile &&  !defaultSel){
            alert("Background image must be selected");
            return;
        }
        form.submit();



    });

    let default_img_box = document.querySelector("#default-images");
    
    default_img_box.addEventListener("click", (e) => {
        if (e.target && e.target.nodeName === "IMG") {
            
            e.target.classList.add("selected");
            file_up.value = "";
            newFile = false;
            defaultSel = true;
            default_imgs.forEach(image => {
                if (e.target.dataset['img'] != image.dataset['img']) {
                    image.classList.remove("selected");
                }
            });
        }
    });
    let clear = document.querySelector("#clear-file");
    clear.addEventListener("click", (e) => {
        e.preventDefault();
        newFile = false;
        defaultSel = false;
        file_up.value = "";
        clearDefault(default_imgs);

    });
});


function randomNumber() {
    let base = 1000000000;
    let rand = Math.floor(Math.random() * base) + base;
    return rand;

}
function clearDefault(default_imgs){
    default_imgs.forEach(image => {

            image.classList.remove("selected");

        });

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
    pill.appendChild(input);
    div.appendChild(pill);
    pill.querySelector(".remove-btn").addEventListener("click", (e) => {
        tagSet.delete(value);
        pill.remove();
    });

}