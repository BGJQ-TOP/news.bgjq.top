<?php
/**
 * 直接测试 admin/login 路由
 * 这个脚本模拟 index.php 的路由处理，但会显示详细的调试信息
 */

// 开启错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Admin/Login 路由测试</h1>";
echo "<hr>";

// 显示请求信息
echo "<h2>请求信息</h2>";
echo "<ul>";
echo "<li>REQUEST_URI: " . htmlspecialchars($_SERVER['REQUEST_URI']) . "</li>";
echo "<li>REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "</li>";
echo "<li>POST 数据：<pre>" . print_r($_POST, true) . "</pre></li>";
echo "</ul>";

// 启动会话
echo "<h2>会话启动</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "<p style='color: green;'>✓ 会话已启动</p>";
} else {
    echo "<p style='color: orange;'>⚠ 会话已经启动</p>";
}
echo "<p>SESSION ID: " . session_id() . "</p>";

// 加载配置文件
echo "<h2>加载配置文件</h2>";
require_once 'config/config.php';
require_once 'core/functions.php';
echo "<p style='color: green;'>✓ 配置文件加载成功</p>";

// 模拟路由处理
echo "<h2>路由处理</h2>";
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = trim($path, '/');

echo "<p>请求路径：<code>{$path}</code></p>";

if (strpos($path, 'test_admin_route.php') === 0) {
    $path = 'admin/login';
    echo "<p style='color: blue;'>ℹ 自动重定向到 admin/login</p>";
}

if (strpos($path, 'admin/') === 0) {
    $adminPath = substr($path, 6);
    echo "<p>后台路径：<code>{$adminPath}</code></p>";
    
    $adminRoutes = [
        'login' => 'AdminAuthController@login',
        'dashboard' => 'AdminController@dashboard',
    ];
    
    if (isset($adminRoutes[$adminPath])) {
        echo "<p style='color: green;'>✓ 找到路由：<code>{$adminRoutes[$adminPath]}</code></p>";
        list($controller, $action) = explode('@', $adminRoutes[$adminPath]);
        echo "<p>控制器：<code>{$controller}</code></p>";
        echo "<p>方法：<code>{$action}</code></p>";
    } else {
        echo "<p style='color: red;'>✗ 路由未找到</p>";
        exit;
    }
} else {
    echo "<p style='color: red;'>✗ 不是 admin 路由</p>";
    exit;
}

// 加载控制器
echo "<h2>加载控制器</h2>";
require_once 'core/BaseModel.php';
require_once 'models/AdminUserModel.php';

$controllerFile = __DIR__ . '/controllers/' . $controller . '.php';
echo "<p>控制器文件：<code>{$controllerFile}</code></p>";

if (file_exists($controllerFile)) {
    echo "<p style='color: green;'>✓ 文件存在</p>";
    require_once $controllerFile;
} else {
    echo "<p style='color: red;'>✗ 文件不存在</p>";
    exit;
}

// 实例化控制器
echo "<h2>实例化控制器</h2>";
if (class_exists($controller)) {
    echo "<p style='color: green;'>✓ 类存在：<code>{$controller}</code></p>";
    $controllerInstance = new $controller();
    echo "<p style='color: green;'>✓ 控制器实例化成功</p>";
} else {
    echo "<p style='color: red;'>✗ 类不存在</p>";
    exit;
}

// 调用方法
echo "<h2>调用方法</h2>";
if (method_exists($controllerInstance, $action)) {
    echo "<p style='color: green;'>✓ 方法存在：<code>{$action}</code></p>";
    echo "<p>准备调用方法...</p>";
    
    // 使用输出缓冲捕获输出
    ob_start();
    $controllerInstance->$action();
    $output = ob_get_clean();
    
    echo "<p style='color: green;'>✓ 方法调用成功</p>";
    echo "<h2>输出内容</h2>";
    echo $output;
} else {
    echo "<p style='color: red;'>✗ 方法不存在</p>";
}

echo "<hr>";
echo "<p><a href='/admin/login'>前往正式登录页面</a></p>";
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1 { color: #333; }
    h2 { color: #666; margin-top: 30px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    code { background: #f5f5f5; padding: 2px 5px; border-radius: 3px; }
    ul { line-height: 1.8; }
</style>
