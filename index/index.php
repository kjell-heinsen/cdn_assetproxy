<?php
// asset-proxy.php
header("Access-Control-Allow-Origin: *");
require_once '../config.php';
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$referer_host = parse_url($referer, PHP_URL_HOST);

if (!in_array($referer_host, $allowed_domains)) {
    http_response_code(403);
    exit('Zugriff verweigert');
}


$path = $_GET['path'] ?? '';

// KRITISCH: Pfad-Traversal verhindern
$path = str_replace(['../', '.\\', '..\\'], '', $path);
$path = ltrim($path, '/');

// Nur erlaubte Zeichen
if (!preg_match('/^[a-zA-Z0-9\/\-_\.]+$/', $path)) {
    http_response_code(400);
    exit('Invalid path characters');
}

// Nur erlaubte Dateierweiterungen
$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$allowed_extensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'woff', 'woff2'];

if (!in_array($extension, $allowed_extensions)) {
    http_response_code(400);
    exit('File type not allowed');
}

// Vollständiger Pfad
$file_path = DOCROOT . '/assets/' . $path;

// Sicherheitsprüfung: Datei muss im assets-Ordner sein
$real_path = realpath($file_path);
$assets_path = realpath(__DIR__ . '/assets/');

if (!$real_path || strpos($real_path, $assets_path) !== 0) {
    http_response_code(403);
    exit('Path not allowed');
}


if (!file_exists($file_path) || !is_file($file_path)) {
    http_response_code(404);
    exit('File not found');
}

// MIME-Type bestimmen
$mime_types = [
    'css' => 'text/css',
    'js' => 'application/javascript',
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif' => 'image/gif',
    'svg' => 'image/svg+xml',
    'woff' => 'font/woff',
    'woff2' => 'font/woff2'
];

header('Content-Type: ' . ($mime_types[$extension] ?? 'application/octet-stream'));
header('Cache-Control: public, max-age=3600');

readfile($file_path);


?>
