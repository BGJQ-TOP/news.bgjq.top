<?php
/**
 * 简单的登录测试
 */

require_once 'config/config.php';
require_once 'core/functions.php';

echo "<h1>登录流程测试</h1>";
echo "<hr>";

// 模拟登录数据
$_POST['username'] = 'LouieMAIN';
$_POST['password'] = 'test'; // 这个密码是错误的，用于测试

echo "<h2>POST 数据</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>测试 AdminUserModel</h2>";
try {
    $model = new AdminUserModel();
    echo "✓ AdminUserModel 实例化成功\n";
    
    $user = $model->getUserByUsername('LouieMAIN');
    if ($user) {
        echo "✓ 找到用户\n";
        echo "<pre>";
        print_r($user);
        echo "</pre>";
        
        // 测试密码验证
        $testPassword = 'test';
        $result = $model->verifyPassword($testPassword, $user['password']);
        echo "<p>密码验证结果：" . ($result ? "通过" : "失败") . "</p>";
    } else {
        echo "✗ 未找到用户\n";
    }
} catch (Exception $e) {
    echo "✗ 错误：" . $e->getMessage() . "\n";
}

echo "<hr>";
echo "<p><a href='/debug_login.php'>查看完整调试信息</a></p>";
?>

<style>
    body { font-family: monospace; margin: 20px; }
    h1 { color: #333; }
    h2 { color: #666; margin-top: 30px; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
</style>
