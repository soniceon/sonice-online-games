<?php
/**
 * Game Page Save API
 * Receives JSON data and saves HTML content to the pages/games directory
 */

// Set response headers
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Only POST requests are allowed']);
    exit;
}

// Read POST data
$postData = file_get_contents('php://input');
$data = json_decode($postData, true);

// Validate data
if (!$data || !isset($data['filename']) || !isset($data['content'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

// Verify filename safety (only allow letters, numbers, hyphens, and underscores)
if (!preg_match('/^[a-zA-Z0-9_\-]+\.html$/', $data['filename'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid filename']);
    exit;
}

// Ensure target directory exists
$targetDir = '../pages/games';
if (!is_dir($targetDir)) {
    if (!mkdir($targetDir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['error' => 'Unable to create target directory']);
        exit;
    }
}

// Complete file path
$filePath = $targetDir . '/' . $data['filename'];

// Save file
if (file_put_contents($filePath, $data['content']) === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save file']);
    exit;
}

// Return success
echo json_encode([
    'success' => true,
    'path' => '/pages/games/' . $data['filename'],
    'fullPath' => $filePath
]); 