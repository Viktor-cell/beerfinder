<?php

function html_from_url($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url); // URL to fetch
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects, if any

    $html = curl_exec($ch);

    curl_close($ch);

    return $html;
}