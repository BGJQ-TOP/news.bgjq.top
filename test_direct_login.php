<?php
/**
 * 直接测试登录功能（绕过路由）
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 启动会话
session_start();

// 加载必要的文件
require_once 'config/config.php';
require_once 'core/functions.php';
require_once 'core/BaseModel.php';
require_once 'models/AdminUserModel.php';
require_once 'controllers/AdminAuthController.php';

// 显示当前状态
echo "<h1>直接登录测试</h1>";
echo "<hr>";

echo "<h2>当前状态</h2>";
echo "<p>SESSION 状态：" . (session_status() === PHP_SESSION_ACTIVE ? "已启动 ✓" : "未启动 ✗") . "</p>";
echo "<p>SESSION ID: " . session_id() . "</p>";
echo "<p>请求方法：<strong>{$_SERVER['REQUEST_METHOD']}</strong></p>";

// 处理登录
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<hr>";
    echo "<h2>POST 数据</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    $username = safe_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    echo "<h2>登录处理</h2>";
    echo "<p>用户名：{$username}</p>";
    
    try {
        $controller = new AdminAuthController();
        
        // 使用反射调用私有方法
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('authenticateUser');
        $method->setAccessible(true);
        
        $user = $method->invoke($controller, $username, $password);
        
        if ($user) {
            echo "<p style='color: green;'>✓ 认证成功！</p>";
            echo "<pre>";
            print_r($user);
            echo "</pre>";
        } else {
            echo "<p style='color: red;'>✗ 认证失败：用户名或密码错误</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ 错误：" . $e->getMessage() . "</p>";
    }
}

// 显示登录表单
echo "<hr>";
echo "<h2>登录表单</h2>";
?>
<form method="post" action="">
    <div style="margin: 10px 0;">
        <label>用户名：<input type="text" name="username" value="LouieMAIN" style="padding: 5px; width: 200px;"></label>
    </div>
    <div style="margin: 10px 0;">
        <label>密码：<input type="password" name="password" style="padding: 5px; width: 200px;"></label>
    </div>
    <div style="margin: 10px 0;">
        <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">登录</button>
    </div>
</form>

<hr>
<p><a href="/admin/login">前往正式登录页面</a></p>
<p><a href="/diagnose.php">返回诊断页面</a></p>
