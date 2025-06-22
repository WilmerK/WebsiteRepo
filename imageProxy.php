<?php
$url = $_GET['url'] ?? '';

$allowedExtensions = '/\.(png|jpe?g|gif|webp|mp4|webm|ogg)$/i';

if (filter_var($url, FILTER_VALIDATE_URL) && preg_match($allowedExtensions, $url)) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'MediaProxy/1.0');
    $data = curl_exec($ch);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    if ($data && preg_match('/^(image|video)\//', $contentType)) {
        header("Content-Type: $contentType");
        echo $data;
        exit;
    }
}

http_response_code(400);
echo 'Invalid image or video';
?>
