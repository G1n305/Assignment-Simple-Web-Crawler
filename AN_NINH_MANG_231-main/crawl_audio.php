<?php
include"function.php";
//Audio downloader
function downloadAudio($url, $folderPath) {
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

    // Tìm kiếm tất cả các thẻ <a>
    $audioTags = $dom->getElementsByTagName('audio');

    $downloadedAudios = []; // Mảng để lưu trữ URL của các tệp âm thanh tải xuống thành công

    // Tải xuống mỗi tệp âm thanh
    foreach ($audioTags as $audioTag) {
        $sourceTags = $audioTag->getElementsByTagName('source');
        foreach ($sourceTags as $sourceTag) {
            $audioUrl = $sourceTag->getAttribute('src');
            $audioUrl = urljoin($url, $audioUrl);
            echo "<p>URL Audio: <a href='$audioUrl' target='_blank'>$audioUrl</a></p>";
            ob_flush();
            flush();

            // Sử dụng cURL để tải tệp âm thanh
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $audioUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $audioData = curl_exec($ch);
            curl_close($ch);

            if ($audioData === false) {
                echo "Failed to download audio from $audioUrl<br>";
                ob_flush();
                flush();
                continue;
            }

            $audioName = $folderPath . '/' . basename($audioUrl);
            if (file_put_contents($audioName, $audioData) === false) {
                echo "Failed to save audio $audioName<br>";
            } else {
                echo "Downloaded: $audioName<br>";
                $downloadedAudios[] = $audioUrl; // Lưu URL vào mảng
            }
            ob_flush();
            flush();
        }
    }

    return $downloadedAudios; // Trả về mảng các URL đã tải xuống
}


 
if (isset($_GET['url']) && isset($_GET['folderPath'])) {
    $url = $_GET['url'];
    $folderPath = &$_GET['folderPath']; // Thay thế bằng đường dẫn thư mục mong muốn
            // Đảm bảo rằng đường dẫn thư mục tồn tại và có thể ghi vào
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true); // Tạo thư mục nếu nó không tồn tại
            }
            //$folderPath = 'D:/database/htdocs/result'; // Thay thế bằng đường dẫn thư mục mong muốn
    $downloadedAudio = downloadAudio($url, $folderPath);

    echo "<p>Crawling data from: $url</p>";
    foreach ($downloadedAudio as $audioUrl) {
        echo "<p>Downloaded Audio URL: $audioUrl</p>";

    }
    ob_flush();
    flush();

} else {
    echo "No URL provided.<br>";
    ob_flush();
    flush();
}
?>