<?php
$url = $_GET['url'] ?? '';
if (filter_var($url, FILTER_VALIDATE_URL) && preg_match('/\.(png|jpe?g|gif|webp)$/i', $url)) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $data = curl_exec($ch);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    if ($data && strpos($contentType, 'image/') === 0) {
        header("Content-Type: $contentType");
        echo $data;
        exit;
    }
}
http_response_code(400);
echo 'Invalid image';
