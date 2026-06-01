document.addEventListener("DOMContentLoaded", () => {
    document.querySelector("#google-signup").addEventListener("click", (e) => {
        // the client id from GCP
        const client_id = "171891580707-lutcvtuh9gnmg9cobs9ud9qtosdichf1.apps.googleusercontent.com"

        // create a CSRF token and store it locally
        const state = [...crypto.getRandomValues(new Uint8Array(16))]
            .map(b => b.toString(16).padStart(2, "0"))
            .join("");

        localStorage.setItem("latestCSRFToken", state);
        //console.log(window.location.origin);
        let scope = encodeURIComponent("openid email profile");
        let redirectURI = encodeURIComponent(`${window.location.origin}/Book Blog/integrations/oauth2/callback`);
        // redirect the user to Google
        const link = `https://accounts.google.com/o/oauth2/v2/auth?client_id=${client_id}` + `&redirect_uri=${redirectURI}&response_type=code&scope=${scope}&state=${state}`;
        console.log(link);
        window.location.assign(link);

    });
});