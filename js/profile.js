document.addEventListener("DOMContentLoaded",()=>{
    document.querySelector("#signout").addEventListener("click",(e)=>{
        fetch("../php/profile_signout.php").then(response =>{
            if(response.ok){
                console.log("okay");
            }else{
                console.log("BAD");
            }
        }).catch(error=> console.error("Network error:",error));
    });
});