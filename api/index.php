<?php
/**
 * API 入口文件
 * 处理所有 /api/* 请求
 */

// 开启错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 获取当前目录的父目录路径
$rootPath = dirname(__DIR__);

// 加载配置文件
require_once $rootPath . '/config/config.php';
require_once $rootPath . '/core/functions.php';

// 启动会话
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// 获取请求路径
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = trim(str_replace('/api/', '', $path), '/');

// API 路由映射
$routes = [
    'login' => ['controller' => 'UserController', 'method' => 'doLogin'],
    'check-login' => ['controller' => 'UserController', 'method' => 'checkLogin'],
    'is-diplomat' => ['controller' => 'UserController', 'method' => 'isDiplomat'],
    'can-publish-diplomat' => ['controller' => 'UserController', 'method' => 'canPublishDiplomatAnnouncement'],
    'diplomat-publish' => ['controller' => 'ContributionController', 'method' => 'publishDiplomatAnnouncement'],
    'contribute' => ['controller' => 'ContributionController', 'method' => 'submit'],
];

// 解析路由
$controller = null;
$action = null;
$params = [];

if (isset($routes[$path])) {
    $controller = $routes[$path]['controller'];
    $action = $routes[$path]['method'];
} else {
    // 尝试前缀匹配
    $pathParts = explode('/', $path);
    $routeKey = $pathParts[0];
    if (isset($routes[$routeKey])) {
        $controller = $routes[$routeKey]['controller'];
        $action = $routes[$routeKey]['method'];
        $params = array_slice($pathParts, 1);
    }
}

// 加载并执行控制器
if ($controller && $action) {
    $controllerFile = $rootPath . '/controllers/' . $controller . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
        // 确保加载了模型
        require_once $rootPath . '/models/' . ($controller === 'UserController' ? 'UserModel' : 'ArticleModel') . '.php';
        
        if (class_exists($controller)) {
            $controllerInstance = new $controller();
            
            if (method_exists($controllerInstance, $action)) {
                call_user_func_array([$controllerInstance, $action], $params);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => '方法不存在: ' . $action]);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => '控制器不存在: ' . $controller]);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => '控制器文件不存在: ' . $controllerFile]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'API 路由不存在: ' . $path]);
}
