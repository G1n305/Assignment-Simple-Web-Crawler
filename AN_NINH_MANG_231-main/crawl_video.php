<?php
//Audio downloader
include"function.php";
function downloadVideo($url, $folderPath) {
    // Tạo context với User-Agent giả mạo
    // Sử dụng cURL để lấy nội dung HTML từ URL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36');
    $html = curl_exec($ch);
    curl_close($ch);

    if ($html === false) {
        echo "Failed to retrieve content from the URL: $url<br>";
        ob_flush();
        flush();
        return;
    }

    // Tạo đối tượng DOMDocument và tải nội dung HTML
    $dom = new DOMDocument;
    @$dom->loadHTML($html);

    // Tìm kiếm tất cả các thẻ <video>
    $vidTags = $dom->getElementsByTagName('video');
    $downloadedVideo = []; // Mảng để lưu trữ URL của các tệp video tải xuống thành công

    // Tải xuống mỗi tệp video
    foreach ($vidTags as $vidTag) {
        $sourceTags = $vidTag->getElementsByTagName('source');
        foreach ($sourceTags as $sourceTag) {
            $vidUrl = $sourceTag->getAttribute('src');
            $vidUrl = urljoin($url, $vidUrl);
            echo "<p>URL Video: <a href='$vidUrl' target='_blank'>$vidUrl</a></p>";
            ob_flush();
            flush();

            // Sử dụng cURL để tải tệp video
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $vidUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $vidData = curl_exec($ch);
            curl_close($ch);

            if ($vidData === false) {
                echo "Failed to download video from $vidUrl<br>";
                ob_flush();
                flush();
                continue;
            }

            $vidName = $folderPath . '/' . basename($vidUrl);
            if (file_put_contents($vidName, $vidData) === false) {
                echo "Failed to save video $vidName<br>";
            } else {
                echo "Downloaded: $vidName<br>";
                $downloadedVideo[] = $vidUrl; // Lưu URL vào mảng
            }
            ob_flush();
            flush();
        }
    }

    return $downloadedVideo; // Trả về mảng các URL đã tải xuống// Trả về mảng các URL đã tải xuống
}


if (isset($_GET['url'])) {
    $url = $_GET['url'];
    $folderPath = 'D:/database/htdocs/resulta'; // Thay thế bằng đường dẫn thư mục mong muốn
    $downloadedVideo = downloadVideo($url, $folderPath);

    echo "<p>Crawling data from: $url</p>";
    foreach ($downloadedVideo as $vidUrl) {
        echo "<p>Downloaded Video URL: $vidUrl</p>";

    }
    ob_flush();
    flush();

} else {
    echo "No URL provided.<br>";
    ob_flush();
    flush();
}
?>