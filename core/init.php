<?php
require_once __DIR__ . '/config/loader.php';

// 获取文件夹中的所有 PHP 文件
$files = glob(__DIR__ . '/../config/*.php');
// 遍历文件数组，引入每个 PHP 文件，并将返回值传递给全局类
foreach ($files as $file) {
    $result = require_once $file; // 或使用 require $file;
    // 去除扩展名部分
    \core\config\Loader::add($result, pathinfo($file, PATHINFO_FILENAME)); // 假设全局类有一个方法 addResult() 来添加结果
}
//公共方法
require_once __DIR__ . "/../app/common/common.php";
//获取配置参数
$setting = \core\config\Loader::get('setting');


//数据库连接
require_once __DIR__ . "/database/database.php";
$db = new Database($setting['db']);


//加载redis
require_once __DIR__ . "/database/cache/redisCacheManager.php";
$redisInstance = RedisCacheManager::getInstance($setting['redis']);
$redis = $redisInstance->getRedis();

// 定义服务目录路径
$serviceDirectory = dirname(dirname(__FILE__)) . '/services/';

// 使用递归遍历获取所有服务文件
$iterator     = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($serviceDirectory));
$serviceFiles = [];
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $serviceFiles[] = $file->getPathname();
    }
}

// 引入所有服务文件
foreach ($serviceFiles as $file) {
    require_once $file;
}

/************************加载路由************************/
//下面处理路由
$pathInfo = isset($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO']) : '';

// 移除路径信息的首尾斜杠
$pathInfo = trim($pathInfo, '/');

// 使用斜杠将路径信息分割成数组
$segments = explode('/', $pathInfo);

// 设置默认值
$module     = $setting['app'];
$controller = $setting['default_controller'];
$method     = $setting['default_method'];

// 从路径信息中获取模块，控制器，和方法
if (isset($segments[0]) && !empty($segments[0])) {
    $module = filter_var($segments[0], FILTER_SANITIZE_STRING);
}
if (isset($segments[1]) && !empty($segments[1])) {
    $controller = filter_var($segments[1], FILTER_SANITIZE_STRING);
}
if (isset($segments[2]) && !empty($segments[2])) {
    $method = filter_var($segments[2], FILTER_SANITIZE_STRING);
}

// 验证模块，控制器，和方法的名称是否有效
// 这里可以添加自定义的验证逻辑
if (!preg_match('/^[a-z0-9_]+$/i', $module) ||
    !preg_match('/^[a-z0-9_]+$/i', $controller) ||
    !preg_match('/^[a-z0-9_]+$/i', $method)) {
    die("Invalid module, controller, or method name.");
}


// ... 解析路径信息并设置 $module, $controller, $method 变量 ...

// 根据解析出的模块、控制器和方法来构建文件路径
$controllerFile = __DIR__ . "/../app/{$module}/controllers/" . ucfirst($controller) . ".php";

// 检查控制器文件是否存在
if (file_exists($controllerFile)) {
    // 包含控制器文件
    require_once $controllerFile;

    // 构建控制器类的名称（包括命名空间）
    $controllerClass = "{$module}\\controllers\\" . ucfirst($controller);

    // 检查控制器类是否存在
    if (class_exists($controllerClass)) {
        // 实例化控制器类
        $controllerInstance = new $controllerClass();

        // 检查方法是否存在
        if (method_exists($controllerInstance, $method)) {
            // 调用方法
            $controllerInstance->$method();
        } else {
            die("Method {$method} not found in controller {$controllerClass}.");
        }
    } else {
        die("Controller class {$controllerClass} not found.");
    }
} else {
    die("Controller file {$controllerFile} not found.");
}



