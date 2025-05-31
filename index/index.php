<?php
header("Access-Control-Allow-Origin: *");
require_once '../config.php';

// Verbesserte Referer-Prüfung mit zusätzlichen Sicherheitsmaßnahmen
function validateReferer($allowed_domains) {
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if (empty($referer)) {
        return false; // Kein Referer = verdächtig
    }

    $referer_host = parse_url($referer, PHP_URL_HOST);
    if (!$referer_host || !in_array($referer_host, $allowed_domains)) {
        return false;
    }

    // Zusätzliche Prüfung: CSRF-Token oder API-Key empfohlen
    return true;
}

if (!validateReferer($allowed_domains)) {
    http_response_code(403);
    exit('Zugriff verweigert');
}

$path = $_GET['path'] ?? '';

// SICHERHEITSKRITISCH: Robuste Path-Traversal-Verhinderung
function sanitizePath($path) {
    // Alle möglichen Traversal-Sequenzen entfernen (mehrfach!)
    do {
        $old_path = $path;
        $path = str_replace([
            '../', '..\\', './', '.\\',
            '..%2f', '..%2F', '..%5c', '..%5C',
            '%2e%2e%2f', '%2e%2e%5c', '%2e%2e/',
            '..../', '....//', '....\\',
            // URL-encoded Varianten
            urldecode('../'), urldecode('..\\')
        ], '', $path);
    } while ($old_path !== $path);

    // Führende Slashes entfernen
    $path = ltrim($path, '/\\');

    return $path;
}

$path = sanitizePath($path);

// Leerer Pfad nach Bereinigung = Angriff
if (empty($path)) {
    http_response_code(400);
    exit('Invalid path');
}

// Striktere Zeichen-Validierung
if (!preg_match('/^[a-zA-Z0-9\/\-_\.]+$/', $path)) {
    http_response_code(400);
    exit('Invalid path characters');
}

// Keine aufeinanderfolgenden Punkte erlauben
if (strpos($path, '..') !== false) {
    http_response_code(400);
    exit('Path traversal detected');
}

// Erweiterte Dateierweiterungsprüfung
$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$allowed_extensions = [
    'css' => 'text/css',
    'js' => 'application/javascript',
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif' => 'image/gif',
    'svg' => 'image/svg+xml',
    'woff' => 'font/woff',
    'woff2' => 'font/woff2',
    'ico' => 'image/x-icon'
];

if (!array_key_exists($extension, $allowed_extensions)) {
    http_response_code(400);
    exit('File type not allowed');
}

// KRITISCH: Sichere Pfad-Konstruktion
$assets_base = realpath(DOCROOT . '/assets/');
if (!$assets_base) {
    http_response_code(500);
    exit('Assets directory not found');
}

$file_path = $assets_base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
$real_path = realpath($file_path);

// Mehrschichtige Sicherheitsprüfung
if (!$real_path) {
    http_response_code(404);
    exit('File not found');
}

// Sicherstellen, dass Datei im erlaubten Bereich liegt
if (strpos($real_path, $assets_base . DIRECTORY_SEPARATOR) !== 0) {
    error_log("Security violation: Attempted access to $real_path from IP " . $_SERVER['REMOTE_ADDR']);
    http_response_code(403);
    exit('Access denied');
}

// Datei existiert und ist reguläre Datei?
if (!is_file($real_path) || !is_readable($real_path)) {
    http_response_code(404);
    exit('File not found');
}

// Zusätzliche Sicherheitsprüfung: Dateigröße begrenzen (z.B. 10MB)
$max_file_size = 20 * 1024 * 1024; // 20MB
if (filesize($real_path) > $max_file_size) {
    http_response_code(413);
    exit('File too large');
}

// Sichere Header setzen
header('Content-Type: ' . $allowed_extensions[$extension]);
header('Cache-Control: public, max-age=3600');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

// Content-Security-Policy für bestimmte Dateitypen
if (in_array($extension, ['js', 'css'])) {
    header("Content-Security-Policy: default-src 'none'");
}

// Datei ausgeben
readfile($real_path);

// Logging für Sicherheitsaudit
error_log("Asset served: $path to " . $_SERVER['REMOTE_ADDR']);
