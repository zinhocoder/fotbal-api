<?php

$apiKey = 'API KEY AQUI';
$apiUrl = 'https://api-football-v1.p.rapidapi.com/v3/';

function callAPI($endpoint, $params = []) {
    global $apiKey, $apiUrl;

    $url = $apiUrl . $endpoint . '?' . http_build_query($params);
    $headers = [
        'X-RapidAPI-Host: api-football-v1.p.rapidapi.com',
        'X-RapidAPI-Key: ' . $apiKey
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);

}
?>