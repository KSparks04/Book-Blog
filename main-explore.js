document.addEventListener('DOMContentLoaded',()=>{
    let result = fetch('get_books.php');
    result.then(data => {return data.json()}).then(results => console.log(results));
});