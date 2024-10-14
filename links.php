<?php
// Kết nối đến cơ sở dữ liệu MySQL
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$host = $url["host"];
$db = substr($url["path"], 1);
$user = $url["user"];
$pass = $url["pass"];

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

// Hàm để rút gọn link sử dụng API TinyURL
function shortenUrl($url) {
    $apiUrl = "https://api.tinyurl.com/v1/create?url=" . urlencode($url);
    $response = file_get_contents($apiUrl);
    $data = json_decode($response, true);
    return $data['tinyurl'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputText = $_POST['inputText'];
    preg_match_all('/https?:\/\/\S+|s\.shopee\.vn\/\S+/', $inputText, $matches);

    foreach ($matches[0] as $match) {
        $originalUrl = $match;
        // Tạo tiền tố
        $prefixedUrl = "https://shope.ee/an_redir?origin_link=" . urlencode($originalUrl) . "&affiliate_id=17305270177&sub_id=0";
        // Rút gọn link
        $shortenedUrl = shortenUrl($prefixedUrl);
        // Thay thế link trong văn bản
        $inputText = str_replace($originalUrl, $shortenedUrl, $inputText);
    }
    echo "<script>alert('Kết quả: " . htmlspecialchars($inputText) . "');</script>";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rút gọn link</title>
</head>
<body>
    <h1>Rút gọn link</h1>
    <form method="post">
        <textarea name="inputText" rows="10" cols="50" placeholder="Nhập văn bản chứa link..."></textarea><br>
        <button type="submit">Tạo tiền tố và rút gọn link</button>
    </form>
</body>
</html>
