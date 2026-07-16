<?php
/**
 * 登录调试 - 检查服务器错误日志
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 先启动会话，再输出任何内容
session_start();

echo "<h1>登录调试 - 错误日志检查</h1>";
echo "<hr>";

// 1. PHP 配置
echo "<h2>1. PHP 配置</h2>";
echo "<ul>";
echo "<li>PHP 版本：" . phpversion() . "</li>";
echo "<li>显示错误：" . ini_get('display_errors') . "</li>";
echo "<li>记录错误：" . ini_get('log_errors') . "</li>";
echo "<li>错误日志路径：" . ini_get('error_log') . "</li>";
echo "</ul>";

// 2. 尝试读取错误日志
echo "<h2>2. 错误日志文件</h2>";
$logFiles = [
    '/var/log/php/error.log',
    '/var/log/nginx/news.bgjq.top.error.log',
    './error.log',
    '../error.log'
];

foreach ($logFiles as $logFile) {
    echo "<p>检查：<code>{$logFile}</code></p>";
    if (file_exists($logFile)) {
        echo "<p style='color: green;'>✓ 文件存在</p>";
        
        // 读取最后 20 行
        $lines = file($logFile);
        $lastLines = array_slice($lines, -20);
        
        if (!empty($lastLines)) {
            echo "<p>最后 20 行：</p>";
            echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 400px; overflow-y: auto;'>";
            echo htmlspecialchars(implode('', $lastLines));
            echo "</pre>";
        }
    } else {
        echo "<p style='color: red;'>✗ 文件不存在</p>";
    }
    echo "<hr>";
}

// 3. 测试登录流程
echo "<h2>3. 登录流程测试</h2>";

echo "<p>SESSION 状态：" . (session_status() === PHP_SESSION_ACTIVE ? "✓ 已启动" : "✗ 未启动") . "</p>";
echo "<p>SESSION ID: " . session_id() . "</p>";

// 加载必要的文件
require_once 'config/config.php';
require_once 'core/functions.php';

echo "<p>配置文件加载：✓</p>";
echo "<p>PASSWORD_SALT: <code>" . PASSWORD_SALT . "</code></p>";

try {
    require_once 'core/BaseModel.php';
    require_once 'models/AdminUserModel.php';
    echo "<p>模型加载：✓</p>";
    
    $model = new AdminUserModel();
    echo "<p>AdminUserModel 实例化：✓</p>";
    
    // 获取秘书长用户
    $user = $model->getUserByUsername('LouieMAIN');
    if ($user) {
        echo "<p>获取用户 LouieMAIN：✓</p>";
        echo "<ul>";
        echo "<li>ID: {$user['id']}</li>";
        echo "<li>用户名：{$user['username']}</li>";
        echo "<li>角色：{$user['role']}</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>✗ 无法获取用户</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ 错误：" . $e->getMessage() . "</p>";
}

// 4. 测试控制器
echo "<h2>4. 控制器测试</h2>";
try {
    require_once 'controllers/AdminAuthController.php';
    echo "<p>AdminAuthController 加载：✓</p>";
    
    $controller = new AdminAuthController();
    echo "<p>AdminAuthController 实例化：✓</p>";
    
    // 使用反射测试 authenticateUser 方法
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('authenticateUser');
    $method->setAccessible(true);
    
    echo "<form method='post' action=''>";
    echo "<div style='margin: 10px 0;'>";
    echo "<label>用户名：<input type='text' name='test_username' value='LouieMAIN' style='padding: 5px;'></label>";
    echo "</div>";
    echo "<div style='margin: 10px 0;'>";
    echo "<label>密码：<input type='password' name='test_password' style='padding: 5px;'></label>";
    echo "</div>";
    echo "<button type='submit' name='test_auth' style='padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;'>测试认证</button>";
    echo "</form>";
    
    if (isset($_POST['test_auth'])) {
        echo "<h3>认证测试结果</h3>";
        $username = safe_input($_POST['test_username'] ?? '');
        $password = $_POST['test_password'] ?? '';
        
        echo "<p>尝试认证 - 用户名：<code>{$username}</code></p>";
        
        $result = $method->invoke($controller, $username, $password);
        
        if ($result) {
            echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; margin: 10px 0;'>";
            echo "<h4 style='color: green;'>✓ 认证成功！</h4>";
            echo "<pre>";
            print_r($result);
            echo "</pre>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; margin: 10px 0;'>";
            echo "<h4 style='color: red;'>✗ 认证失败</h4>";
            echo "<p>用户名或密码错误</p>";
            echo "</div>";
        }
    }
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; margin: 10px 0;'>";
    echo "<h4 style='color: red;'>✗ 控制器错误</h4>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='/admin/login'>前往登录页面</a> | <a href='/diagnose.php'>诊断页面</a></p>";
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1 { color: #333; }
    h2 { color: #666; margin-top: 30px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; white-space: pre-wrap; word-wrap: break-word; }
    code { background: #f5f5f5; padding: 2px 5px; border-radius: 3px; }
    ul { line-height: 1.8; }
</style>
