<?php 
    function fetchData($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "User-Agent: Mozilla/5.0"
    ]);

    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch,CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 10 );
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        die("cURL error: " . curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 400) {
        die("HTTP error: " . $httpCode);
    }

    return $response;
}

?>