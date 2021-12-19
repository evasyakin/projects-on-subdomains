<?php
/**
 * Скрипт для поддержки проектов на субдоменах 3-го уровня.
 * @author Egor Vasyakin <egor@evas-php.com>
 * @see My perfect PHP framework https://evas-php.com/
 * @license MIT
 * PHP 7.2+
 */

// имя публичной директории public (часто бывает public_html)
$publicDirName = 'public';

// поддерживаемые субдомены (субдомен => проект)
$subDomains = [
    'admin' => 'admin-service', 
    'auth' => 'auth-service',
    'pay' => 'pay-service',
];

// поддерживаемые ассеты (расширение => mime-тип)
$mimeTypes = [
    'css' => 'text/css',
    'js' => 'application/javascript',
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'svg' => 'image/svg+xml',
    'gif' => 'image/gif',
    'mp4' => 'video/mp4',
];

// определяем имя проекта
$domain = $_SERVER['SERVER_NAME'];
$domainParts = explode('.', $domain);
$project = array_shift($domainParts);
if (($newDomain = $subDomains[$project] ?? null)) {
    $project = $newDomain;
}
// если имя проекта не найдено в субдоменах, 
// то именем проекта будет домен 2-го уровня.

// пробрасываем ассеты или index.php
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$urlParts = explode('.', $url);
$ext = array_pop($urlParts);

if ($ext && ($mime = $mimeTypes[$ext] ?? null)) {
    // устанавливаем mime-тип ассета
    header("Content-Type: $mime");
    $filename = $url;
} else {
    $filename = 'index.php';
}

// собираем путь к файлу проекта
$filename = __DIR__ . "/../$project/$publicDirName/$filename";

// если файл не найден, 404
if (!is_file($filename) || !is_readable($filename)) {
    header('404 Not found');
    die('<!DOCTYPE html><html><head><meta name="robots" content="noindex, nofollow"></head><body>404 Not found</body></html>');
}

// показываем содержимое файла
call_user_func(function() use ($filename) {
    require_once $filename;
});
