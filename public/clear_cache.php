<?php
// 自动清理 Twig 缓存目录
$cacheDir = __DIR__ . '/../cache';

function rrmdir($dir) {
    if (!is_dir($dir)) return;
    $objects = scandir($dir);
    foreach ($objects as $object) {
        if ($object != "." && $object != "..") {
            $file = $dir . DIRECTORY_SEPARATOR . $object;
            if (is_dir($file)) {
                rrmdir($file);
            } else {
                unlink($file);
            }
        }
    }
    rmdir($dir);
}

// 清理并重建缓存目录
if (is_dir($cacheDir)) {
    rrmdir($cacheDir);
}
mkdir($cacheDir, 0777, true);

echo "Twig 缓存已清理！";

