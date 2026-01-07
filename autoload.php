<?php

spl_autoload_register('autoloadClass');
function autoloadClass($class): void
{
    if ($class === 'Database') {
        $dbPath = __DIR__ . DIRECTORY_SEPARATOR . 'db_connect.php';
        if (file_exists($dbPath)) {
            require_once $dbPath;
            return;
        }
    }
    $directories = [
        __DIR__,
        __DIR__ . DIRECTORY_SEPARATOR . 'classes',
    ];

    foreach ($directories as $directory) {
        $path = buildClassPath($directory, $class);
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
}

function buildClassPath(string $baseDir, string $class): string
{
    $classPath = str_replace(["\\", "/"], DIRECTORY_SEPARATOR, $class);
    return rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($classPath, DIRECTORY_SEPARATOR) . '.php';
}
