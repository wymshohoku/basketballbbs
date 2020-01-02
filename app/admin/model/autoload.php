<?php
spl_autoload_register(function ($class) {
    $prefix = 'model\\';
    $base_dir = __DIR__ .'/';
    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }
    $relative_class = substr($class, $len);
    // 兼容Linux文件找。Windows 下（/ 和 \）是通用的
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.class.php';
    if (file_exists($file)) {
        require $file;
    }
});
