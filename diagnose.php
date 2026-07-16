<?php
/**
 * 完整的登录诊断页面
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>登录功能完整诊断</h1>";
echo "<hr>";

// 1. 检查 PHP 配置
echo "<h2>1. PHP 配置</h2>";
echo "<ul>";
echo "<li>PHP 版本：" . phpversion() . "</li>";
echo "<li>显示错误：" . ini_get('display_errors') . "</li>";
echo "<li>错误报告级别：" . error_reporting() . "</li>";
echo "<li>会话状态：" . (session_status() === PHP_SESSION_ACTIVE ? "已启动" : "未启动") . "</li>";
echo "</ul>";

// 2. 检查 SESSION
echo "<h2>2. SESSION 测试</h2>";
session_start();
$_SESSION['test'] = 'test_value';
echo "<p>SESSION 写入测试：" . $_SESSION['test'] . "</p>";
echo "<p>SESSION ID: " . session_id() . "</p>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// 3. 检查 POST 处理
echo "<h2>3. POST 处理</h2>";
echo "<form method='post' action=''>";
echo "<input type='text' name='test_username' placeholder='输入测试用户名' style='display:block; margin: 10px 0;'>";
echo "<button type='submit' name='test_submit'>提交测试</button>";
echo "</form>";

if (isset($_POST['test_submit'])) {
    echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border: 1px solid #c3e6cb;'>";
    echo "<strong>✓ POST 成功！</strong><br>";
    echo "用户名：" . htmlspecialchars($_POST['test_username'] ?? '无');
    echo "</div>";
}

// 4. 检查文件包含
echo "<h2>4. 文件包含测试</h2>";
$files = [
    'config/config.php',
    'core/functions.php',
    'models/AdminUserModel.php',
    'controllers/AdminAuthController.php'
];

foreach ($files as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        echo "<p style='color: green;'>✓ {$file} 存在</p>";
    } else {
        echo "<p style='color: red;'>✗ {$file} 不存在</p>";
    }
}

// 5. 检查数据库
echo "<h2>5. 数据库测试</h2>";
try {
    require_once 'config/config.php';
    require_once 'core/functions.php';
    
    $db = getDbConnection();
    if ($db) {
        echo "<p style='color: green;'>✓ 数据库连接成功</p>";
        
        // 测试查询
        $sql = "SELECT COUNT(*) as count FROM users";
        $stmt = $db->query($sql);
        $result = $stmt->fetch();
        echo "<p>users 表中有 {$result['count']} 个用户</p>";
        
        // 测试秘书长用户
        $sql = "SELECT username, role FROM users WHERE role = 'secretary_general'";
        $stmt = $db->query($sql);
        $secretaries = $stmt->fetchAll();
        if (count($secretaries) > 0) {
            echo "<p style='color: green;'>✓ 找到 " . count($secretaries) . " 个秘书长用户</p>";
            echo "<ul>";
            foreach ($secretaries as $sec) {
                echo "<li>{$sec['username']} ({$sec['role']})</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: orange;'>⚠ 未找到秘书长用户</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ 数据库连接失败</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ 错误：" . $e->getMessage() . "</p>";
}

// 6. 检查 AdminUserModel
echo "<h2>6. AdminUserModel 测试</h2>";
try {
    $model = new AdminUserModel();
    echo "<p style='color: green;'>✓ AdminUserModel 实例化成功</p>";
    
    // 测试获取用户
    $user = $model->getUserByUsername('LouieMAIN');
    if ($user) {
        echo "<p style='color: green;'>✓ 成功获取用户 LouieMAIN</p>";
        echo "<pre>";
        print_r($user);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>✗ 无法获取用户 LouieMAIN</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ 错误：" . $e->getMessage() . "</p>";
}

// 7. 检查路由
echo "<h2>7. 路由检查</h2>";
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/';
echo "<ul>";
echo "<li>REQUEST_URI: {$requestUri}</li>";
echo "<li>SCRIPT_NAME: {$scriptName}</li>";
echo "<li>PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'N/A') . "</li>";
echo "</ul>";

// 8. 检查错误日志
echo "<h2>8. 错误日志配置</h2>";
echo "<ul>";
echo "<li>log_errors: " . ini_get('log_errors') . "</li>";
echo "<li>error_log: " . ini_get('error_log') . "</li>";
echo "</ul>";

echo "<hr>";
echo "<h2>建议操作</h2>";
echo "<ol>";
echo "<li>访问 <a href='/test_form.php'>/test_form.php</a> 测试表单渲染</li>";
echo "<li>访问 <a href='/test_post.php'>/test_post.php</a> 测试 POST 提交</li>";
echo "<li>访问 <a href='/admin/login'>/admin/login</a> 测试实际登录</li>";
echo "<li>查看服务器错误日志文件</li>";
echo "<li>检查 Web 服务器（Apache/Nginx）配置是否正确处理 PHP</li>";
echo "</ol>";
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1 { color: #333; }
    h2 { color: #666; margin-top: 30px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    ul { line-height: 1.8; }
</style>
