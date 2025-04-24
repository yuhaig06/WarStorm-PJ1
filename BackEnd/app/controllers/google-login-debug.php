<?php
function writeLog($message) {
    $logFile = __DIR__ . '/google-login.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

// Thêm vào đầu file login-google.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ghi log các bước quan trọng
writeLog('Starting Google login process');
writeLog('GET params: ' . print_r($_GET, true));
writeLog('Session data: ' . print_r($_SESSION, true));

// Trong các bước xử lý
if (isset($_GET['code'])) {
    writeLog('Received Google code: ' . $_GET['code']);
    // ... xử lý tiếp
}

// Nếu có lỗi
if (isset($token_data['error'])) {
    writeLog('Token error: ' . print_r($token_data, true));
}

if (curl_errno($ch)) {
    writeLog('CURL error: ' . curl_error($ch));
}
?>