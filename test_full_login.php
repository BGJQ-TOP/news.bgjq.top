<?php
/**
 * 测试完整的登录流程
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 启动会话
session_start();

// 加载必要的文件
require_once 'config/config.php';
require_once 'core/functions.php';

echo "<h1>完整登录流程测试</h1>";
echo "<hr>";

// 显示当前状态
echo "<h2>1. 会话状态</h2>";
echo "<p>SESSION 状态：" . (session_status() === PHP_SESSION_ACTIVE ? "<strong style='color: green;'>已启动 ✓</strong>" : "<strong style='color: red;'>未启动 ✗</strong>") . "</p>";
echo "<p>SESSION ID: " . session_id() . "</p>";

// 测试数据库连接
echo "<h2>2. 数据库连接</h2>";
try {
    $db = getDbConnection();
    echo "<p style='color: green;'>✓ 数据库连接成功</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ 数据库连接失败：" . $e->getMessage() . "</p>";
}

// 获取秘书长用户
echo "<h2>3. 秘书长用户</h2>";
try {
    $db = getDbConnection();
    $sql = "SELECT id, username, role, password FROM users WHERE role = 'secretary_general' LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p style='color: green;'>✓ 找到秘书长用户</p>";
        echo "<ul>";
        echo "<li>ID: {$user['id']}</li>";
        echo "<li>用户名：{$user['username']}</li>";
        echo "<li>角色：{$user['role']}</li>";
        echo "<li>密码哈希：" . substr($user['password'], 0, 30) . "...</li>";
        echo "</ul>";
        
        // 测试密码验证
        echo "<h3>密码验证测试</h3>";
        echo "<form method='post' action=''>";
        echo "<div style='margin: 10px 0;'>";
        echo "<label>测试密码：<input type='password' name='test_password' style='padding: 5px;'></label>";
        echo "</div>";
        echo "<button type='submit' name='test_verify' style='padding: 5px 15px;'>验证密码</button>";
        echo "</form>";
        
        if (isset($_POST['test_verify'])) {
            $testPassword = $_POST['test_password'] ?? '';
            $result = password_verify($testPassword . PASSWORD_SALT, $user['password']);
            echo "<p style='color: " . ($result ? 'green' : 'red') . ";'>";
            echo ($result ? "✓ 密码正确" : "✗ 密码错误");
            echo "</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ 未找到秘书长用户</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ 错误：" . $e->getMessage() . "</p>";
}

// 测试 AdminUserModel
echo "<h2>4. AdminUserModel 测试</h2>";
try {
    require_once 'core/BaseModel.php';
    require_once 'models/AdminUserModel.php';
    
    $model = new AdminUserModel();
    echo "<p style='color: green;'>✓ AdminUserModel 实例化成功</p>";
    
    // 测试获取用户
    $user = $model->getUserByUsername('LouieMAIN');
    if ($user) {
        echo "<p style='color: green;'>✓ 成功获取用户 LouieMAIN</p>";
        echo "<p>用户 ID: {$user['id']}</p>";
        echo "<p>用户名：{$user['username']}</p>";
        echo "<p>角色：{$user['role']}</p>";
    } else {
        echo "<p style='color: red;'>✗ 无法获取用户 LouieMAIN</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ 错误：" . $e->getMessage() . "</p>";
}

// 测试完整的登录流程
echo "<h2>5. 完整登录流程测试</h2>";
echo "<form method='post' action=''>";
echo "<div style='margin: 10px 0;'>";
echo "<label>用户名：<input type='text' name='username' value='LouieMAIN' style='padding: 5px; width: 200px;'></label>";
echo "</div>";
echo "<div style='margin: 10px 0;'>";
echo "<label>密码：<input type='password' name='password' style='padding: 5px; width: 200px;'></label>";
echo "</div>";
echo "<button type='submit' name='do_login' style='padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;'>测试登录</button>";
echo "</form>";

if (isset($_POST['do_login'])) {
    echo "<hr>";
    echo "<h3>登录测试结果</h3>";
    
    try {
        require_once 'core/BaseModel.php';
        require_once 'models/AdminUserModel.php';
        require_once 'controllers/AdminAuthController.php';
        
        $controller = new AdminAuthController();
        
        // 使用反射调用私有方法
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('authenticateUser');
        $method->setAccessible(true);
        
        $username = safe_input($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        echo "<p>尝试登录：用户名 = {$username}</p>";
        
        $user = $method->invoke($controller, $username, $password);
        
        if ($user) {
            echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; margin: 10px 0;'>";
            echo "<h4 style='color: green;'>✓ 登录成功！</h4>";
            echo "<pre>";
            print_r($user);
            echo "</pre>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; margin: 10px 0;'>";
            echo "<h4 style='color: red;'>✗ 登录失败</h4>";
            echo "<p>用户名或密码错误</p>";
            echo "</div>";
        }
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; margin: 10px 0;'>";
        echo "<h4 style='color: red;'>✗ 错误</h4>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
}

echo "<hr>";
echo "<h2>导航</h2>";
echo "<ul>";
echo "<li><a href='/test_direct_login.php'>直接登录测试（绕过路由）</a></li>";
echo "<li><a href='/diagnose.php'>完整诊断页面</a></li>";
echo "<li><a href='/admin/login'>正式登录页面</a></li>";
echo "</ul>";
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1 { color: #333; }
    h2 { color: #666; margin-top: 30px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
    h3 { color: #555; margin-top: 20px; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    ul { line-height: 1.8; }
</style>
