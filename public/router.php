<?php
// php -S localhost:8001 router.php
if (file_exists(__DIR__  . '/' . $_SERVER["REQUEST_URI"])) {
    return false;    // сервер возвращает файлы напрямую.
} else {
   include 'index.php';
}
?>