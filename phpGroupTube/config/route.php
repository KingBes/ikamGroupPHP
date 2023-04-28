<?php

/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Webman\Route;

/** @var  $dir_iterator *递归遍历目录查找控制器自动设置路由 */
// $dir_iterator = new \RecursiveDirectoryIterator(app_path());
$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(app_path()));
/** @var  $suffix *读取config*/
$suffix = config('app.controller_suffix', '');
$suffix_length = strlen((string)$suffix);
// $arr = [];
foreach ($iterator as $file) {
    /** 忽略目录和非php文件 */
    if (is_dir($file) || $file->getExtension() != 'php') {
        continue;
    }

    $file_path = str_replace('\\', '/', $file->getPathname());
    /** 文件路径里不带controller的文件忽略 */
    if (!str_contains(strtolower($file_path), '/controller/')) {
        continue;
    }

    /**  只处理带 controller_suffix 后缀的 */
    if ($suffix_length && substr($file->getBaseName('.php'), -$suffix_length) !== $suffix) {
        continue;
    }

    // 根据文件路径是被类名
    /** @var  $class_name *根据文件路径获取类名 */
    $class_name = str_replace('/', '\\', substr(substr($file_path, strlen(base_path())), 0, -4));

    if (!class_exists((string)$class_name)) {
        echo "Class $class_name not found, skip route for it\n";
        continue;
    }
    $controller = new \ReflectionClass($class_name);
    foreach ($controller->getMethods(\ReflectionMethod::IS_PUBLIC) as $v) {

        /** 淘汰方法 */
        if (in_array($v->name, ['__construct', '__destruct'])) {
            continue;
        }

        $class = new \ReflectionMethod($v->class, $v->name);
        $zj = $class->getAttributes();
        if (isset($zj[0])) {
            $arguments = $zj[0]->getArguments();

            if (isset($arguments['path']) && !isset($arguments['request'])) {
                if (isset($arguments['middleware'])) {
                    Route::any($arguments['path'], [$v->class, $v->name])
                        ->middleware($arguments['middleware'])->name(substr(str_replace("/", ".", $arguments['path']), 1));
                } else {
                    Route::any($arguments['path'], [$v->class, $v->name])->name(substr(str_replace("/", ".", $arguments['path']), 1));
                }
            } elseif (isset($arguments['path']) && isset($arguments['request'])) {
                if (isset($arguments['middleware'])) {
                    Route::add(explode(',', $arguments['request'] . ',OPTIONS'), $arguments['path'], [$v->class, $v->name])
                        ->middleware($arguments['middleware'])->name(substr(str_replace("/", ".", $arguments['path']), 1));
                } else {
                    Route::add(explode(',', $arguments['request'] . ',OPTIONS'), $arguments['path'], [$v->class, $v->name])
                        ->name(substr(str_replace("/", ".", $arguments['path']), 1));
                }
            }
        }
    }
}

//关闭默认路由
Route::disableDefaultRoute();
