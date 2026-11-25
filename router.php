<?php
// Routeur du serveur PHP intégré (site + images) : php -S 0.0.0.0:9990 router.php
// Le site vit sous /siteSAE2A (chemin attendu par FrontControleur), les images sont aussi servies à la racine (Blazor / Android).
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');

if ($path === '/' || $path === '/siteSAE2A' || $path === '/siteSAE2A/') {
    header('Location: /siteSAE2A/home');
    return true;
}

$site = __DIR__ . '/siteSAE2A';
$rel = preg_replace('#^/siteSAE2A#', '', $path);
$file = realpath($site . $rel);

// Fichier statique existant dans siteSAE2A/ (jamais de remontée hors du dossier ni de .php exposé)
if ($file && is_file($file) && str_starts_with($file, realpath($site) . DIRECTORY_SEPARATOR)
    && !preg_match('/\.(php|twig|json|lock)$/i', $file) && !preg_match('#[\\/](vendor|config|tests)[\\/]#', $file)) {
    // mime_content_type() n'est pas toujours activé (ext fileinfo) : table fixe
    $types = ['css' => 'text/css', 'js' => 'application/javascript', 'svg' => 'image/svg+xml',
              'otf' => 'font/otf', 'woff2' => 'font/woff2', 'png' => 'image/png', 'jpg' => 'image/jpeg',
              'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'webp' => 'image/webp', 'ico' => 'image/x-icon'];
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    header('Content-Type: ' . ($types[$ext] ?? 'application/octet-stream'));
    header('Access-Control-Allow-Origin: *');
    readfile($file);
    return true;
}

// Sinon : contrôleur frontal du site
chdir($site);
$_SERVER['SCRIPT_NAME'] = '/siteSAE2A/index.php';
require $site . '/index.php';
