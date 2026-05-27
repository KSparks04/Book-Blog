<?php 
require_once("fetcher.php");

class OpenLibraryClient{
    public function searchBooks($query, $page = 1)
    {
        $url = "https://openlibrary.org/search.json?subject=" . urlencode($query)."&page=$page";
        $response = fetchData($url);

        

        return json_decode($response, true);
    }

    public function getWork($workKey){
        $workUrl = "https://openlibrary.org" . $workKey . ".json";

            $workResponse = fetchData($workUrl);
            return json_decode($workResponse, true);

            
    }

    public function getEdition($editionKey){
        $editionUrl = "https://openlibrary.org/books/" . $editionKey . ".json";

            $editionResponse = fetchData($editionUrl);
            return json_decode($editionResponse, true);
    }
    
}
?>