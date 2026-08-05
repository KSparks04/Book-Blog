document.addEventListener('DOMContentLoaded', () => {

    fetch("php/get_books_reviews.php").then(results => results.json()).then(data => {

        //console.log(data);
        loadBookCards(data);
    });
    fetch("php/get_pop_boards.php").then(results => results.json()).then(boards => {
        loadBoards(boards);
    });
    let profileModal = document.querySelector(".modal");
    document.querySelector("#login-btn")?.addEventListener('click', () => {
        profileModal.style.display = "block";
    });
    //alert(document.querySelector(".close"));
    document.querySelector("span.close").addEventListener('click', () => {
        profileModal.style.display = "none";
    })
    window.onclick = function (event) {
        if (event.target == profileModal) {
            profileModal.style.display = "none";
        }
    }

    let loggedModal = document.querySelector("#profile-modal");
    document.querySelector("#profile-view")?.addEventListener("click", () => {
        loggedModal.style.display = "block";
    })
    document.querySelector("#profile-view")?.addEventListener("mouseover", () => {
        loggedModal.style.display = "block";
    })
    document.querySelector("#profile-view")?.addEventListener("mouseout", () => {
        loggedModal.style.display = "none";
    })
    document.querySelector("#profile-wrapper")?.addEventListener("mouseover", () => {
        loggedModal.style.display = "block";
    })
    document.querySelector("#profile-wrapper")?.addEventListener("mouseout", () => {
        loggedModal.style.display = "none";
    })

    document.querySelector("#sign-up-form")?.addEventListener("submit", (e) => {

        const errors = [];
        let emailError = document.querySelector("#email-error");
        emailError.style.color = "red";
        let email = document.getElementById("email").value.trim();
        if (email === "") {
            emailError.textContent = "Email is required";

            errors.push("Email Missing");


        } else {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailRegex.test(email)) {
                emailError.textContent = "Please enter a valid email address";
                errors.push("Please enter a valid email address.");
            }
        }
        let username = document.getElementById("username").value.trim();
        if (username === "") {
            errors.push("Username is required");
        } else {
            if (username.length > 8 || username.length < 3) {
                errors.push("Username must be at least 3 characters and less than 8");
            }
        }

        let password = document.getElementById("password").value.trim();
        let passwordError = document.getElementById("pwrd-error");
        passwordError.style.color = "red";
        if (password === "") {
            passwordError.textContent = "Password is required";
            errors.push("Password missing");
        } else {
            if (password.length < 8) {
                passwordError.textContent = "Password must be at least 8 characters";
                errors.push("Password wrong");
            }
            if (!/[A-Z]/.test(password)) {
                passwordError.textContent = "Password needs at least one uppercase letter";
                errors.push("Password");
            }
            if (!/[a-z]/.test(password)) {
                passwordError.textContent = "Password needs at least one lowercase letter";
                errors.push("Password");
            }
            if (!/[0-9]/.test(password)) {
                passwordError.textContent = "Password needs at least one number";
                errors.push("Password");
            }
            if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                passwordError.textContent = "Password must contain a special character";
                errors.push("Password must contain a special character.");
            }
        }


        if (errors.length > 0) {
            e.preventDefault();

            //alert(errors.join("\n"));
        }
    });
    document.querySelector("#signout").addEventListener("click", (e) => {
        fetch("php/profile_signout.php").then(response => {
            if (response.ok) {
                console.log("okay");
                window.location.reload();
            } else {
                console.log("BAD");
            }
        }).catch(error => console.error("Network error:", error));
    });

    document.querySelector("#acct-login")?.addEventListener("click", createLoginForm);
});
function createLoginForm() {
    let mainDiv = document.querySelector("#acct");
    mainDiv.innerHTML = "";
    let header = document.createElement("h3");
    header.textContent = "Please Log in";
    mainDiv.appendChild(header);
    let form = document.createElement("form");
    form.setAttribute("id", "login-form");
    form.setAttribute("action", "success-login");
    form.setAttribute("method", "post");

    let labelEmail = document.createElement("label");
    labelEmail.setAttribute("for", "email");
    labelEmail.textContent = "Email Address";
    form.appendChild(labelEmail);
    let email = document.createElement("input");
    email.setAttribute("type", "text");
    email.setAttribute("name", "email");
    email.setAttribute("id", "email");
    form.appendChild(email);

    let labelPwd = document.createElement("label");
    labelPwd.setAttribute("for", "password");
    labelPwd.textContent = "Password";
    form.appendChild(labelPwd);
    let password = document.createElement("input");
    password.setAttribute("type", "password");
    password.setAttribute("name", "password");
    password.setAttribute("id", "password");
    form.appendChild(password);

    let pError = document.createElement("p");
    form.appendChild(pError);
    let btn = document.createElement("button");
    btn.setAttribute("type", "submit");
    btn.setAttribute("id", "login-btn");
    btn.textContent = "Login with email";
    form.appendChild(btn);
    mainDiv.appendChild(form);

    let accountCheck = document.createElement("p");
    accountCheck.textContent = "Don't have an account?";

    let signup = document.createElement("a");
    signup.setAttribute("id", "acct-login");
    signup.textContent = "Sign up";
    signup.addEventListener("click", createSignupForm);
    accountCheck.appendChild(signup);

    mainDiv.appendChild(accountCheck);
}
function createSignupForm() {
    let mainDiv = document.querySelector("#acct");
    mainDiv.innerHTML = "";

    let header = document.createElement("h3");
    header.textContent = "Create your account";
    mainDiv.appendChild(header);


    const form = document.createElement("form");
    form.id = "sign-up-form";
    form.action = "success-signup";
    form.method = "post";


    let usernameLabel = document.createElement("label");
    usernameLabel.htmlFor = "username";
    usernameLabel.textContent = "Displayed Username";

    let usernameInput = document.createElement("input");
    usernameInput.type = "text";
    usernameInput.name = "username";
    usernameInput.id = "username";

    let usernameError = document.createElement("p");
    usernameError.id = "user-error";


    let emailLabel = document.createElement("label");
    emailLabel.htmlFor = "email";
    emailLabel.textContent = "Email Address";

    let emailInput = document.createElement("input");
    emailInput.type = "text";
    emailInput.name = "email";
    emailInput.id = "email";

    let emailError = document.createElement("p");
    emailError.id = "email-error";


    let passwordLabel = document.createElement("label");
    passwordLabel.htmlFor = "password";
    passwordLabel.textContent = "Password";

    let passwordInput = document.createElement("input");
    passwordInput.type = "password";
    passwordInput.name = "password";
    passwordInput.id = "password";

    let passwordError = document.createElement("p");
    passwordError.id = "pwrd-error";


    let submit = document.createElement("button");
    submit.type = "submit";
    submit.id = "sign-up-btn";
    submit.textContent = "Sign up with email";


    form.appendChild(usernameLabel);
    form.appendChild(usernameInput);
    form.appendChild(usernameError);

    form.appendChild(emailLabel);
    form.appendChild(emailInput);
    form.appendChild(emailError);

    form.appendChild(passwordLabel);
    form.appendChild(passwordInput);
    form.appendChild(passwordError);

    form.appendChild(submit);


    const footer = document.createElement("p");
    footer.append("Already have an account? ");

    const loginLink = document.createElement("a");
    loginLink.id = "acct-login";
    loginLink.textContent = "Login";
    loginLink.addEventListener("click", createLoginForm);

    footer.appendChild(loginLink);


    mainDiv.appendChild(header);
    mainDiv.appendChild(form);
    mainDiv.appendChild(footer);


}
function loadBookCards(books) {
    let cardCarousel = document.querySelector(".books-carousel");

    books.forEach(book => {
        //TODO: ADD RATING CHECK WITH population
        // if (book.avg_rating >= 4) {
        let card = document.createElement("div");
        card.classList.add("card");
        card.classList.add("caro-card");
        card.classList.add("card-book");

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
        let bookContent = document.createElement("div");
        bookContent.classList.add("book-text");
        bookContent.classList.add("hide");
        let title = document.createElement("h2");
        title.classList.add("card-title");

        title.classList.add("title2");
        title.textContent = book.title;
        let details = document.createElement("h2");
        details.classList.add("card-details");

        details.classList.add("title3");
        details.textContent = "By " + book.author;
        bookContent.appendChild(title);
        bookContent.appendChild(details);

        console.log(book.avg_rating);
        let star = document.createElement("i");
        star.classList.add("bi");
        star.classList.add("bi-star-fill");
        let divSt = document.createElement("div");
        divSt.classList.add("book-star");
        divSt.appendChild(star);
        if (book.avg_rating != null) {
            let rate = document.createElement("p");
            rate.textContent = Math.round(book.avg_rating);
            divSt.appendChild(rate);
        }

        a.appendChild(img);
        a.appendChild(divSt);
        a.appendChild(bookContent);
        card.addEventListener("mouseover", () => {
            bookContent.classList.remove("hide");
        })
        card.addEventListener("mouseout", () => {
            bookContent.classList.add("hide");
        })
        content.appendChild(a);

        cardCarousel.appendChild(card);
        card.appendChild(content);
    }


        //}
    );
}
function loadBoards(boards) {
    let boardCarousel = document.querySelector(".boards-carousel");
    boardCarousel.innerHTML = "";
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
            details.textContent = user[0].username;
            textDiv.appendChild(details);
            textDiv.appendChild(title);




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
// const elements = document.querySelectorAll('.animate-on-scroll');

//     const observer = new IntersectionObserver(entries => {
//         entries.forEach(entry => {
//             if (entry.isIntersecting) {
//                 entry.target.classList.add('visible');
//             }
//         });
//     }, { threshold: 0.2 });

//     elements.forEach(el => observer.observe(el));
