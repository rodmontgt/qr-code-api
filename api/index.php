<?php
/**
 * QR Code Generator API with Basic Authentication
 * Docker-compatible version
 */

// Configuration from environment variables
define('API_USERNAME', getenv('API_USERNAME') ?: 'admin');
define('API_PASSWORD', getenv('API_PASSWORD') ?: 'changeme');
define('DEFAULT_SIZE', 300);
define('MAX_SIZE', 1000);

// Basic Authentication
function authenticate() {
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
        header('WWW-Authenticate: Basic realm="QR Code API"');
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode(['error' => 'Authentication required']);
        exit;
    }
    
    if ($_SERVER['PHP_AUTH_USER'] !== API_USERNAME || 
        $_SERVER['PHP_AUTH_PW'] !== API_PASSWORD) {
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }
}

// Validate URL
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

// Generate QR Code using Google Charts API
function generateQRCode($data, $size = DEFAULT_SIZE) {
    $size = min(max($size, 100), MAX_SIZE);
    
    $apiUrl = 'https://chart.googleapis.com/chart';
    $params = [
        'cht' => 'qr',
        'chs' => $size . 'x' . $size,
        'chl' => urlencode($data),
        'choe' => 'UTF-8'
    ];
    
    $url = $apiUrl . '?' . http_build_query($params);
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10
        ]
    ]);
    
    $imageData = @file_get_contents($url, false, $context);
    
    if ($imageData === false) {
        return false;
    }
    
    return $imageData;
}

// Main execution
authenticate();

$url = $_REQUEST['url'] ?? '';
$size = intval($_REQUEST['size'] ?? DEFAULT_SIZE);
$format = $_REQUEST['format'] ?? 'image';

if (empty($url)) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => 'URL parameter is required']);
    exit;
}

if (!isValidUrl($url)) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid URL format']);
    exit;
}

$qrImage = generateQRCode($url, $size);

if ($qrImage === false) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Failed to generate QR code']);
    exit;
}

if ($format === 'base64') {
    header('Content-Type: application/json');
    $base64 = base64_encode($qrImage);
    echo json_encode([
        'success' => true,
        'url' => $url,
        'size' => $size,
        'image' => 'data:image/png;base64,' . $base64
    ]);
} else {
    header('Content-Type: image/png');
    header('Content-Length: ' . strlen($qrImage));
    header('Content-Disposition: inline; filename="qrcode.png"');
    echo $qrImage;
}
?>