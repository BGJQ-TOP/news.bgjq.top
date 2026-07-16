<?php
/**
 * 测试密码验证
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/config.php';
require_once 'core/functions.php';

echo "<h1>密码验证测试</h1>";
echo "<hr>";

// 显示配置
echo "<h2>配置信息</h2>";
echo "<p>PASSWORD_SALT: <code>" . PASSWORD_SALT . "</code></p>";

// 连接数据库获取用户
try {
    $db = getDbConnection();
    $sql = "SELECT id, username, role, password FROM users WHERE role = 'secretary_general' LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<h2>用户信息</h2>";
        echo "<p>用户名：{$user['username']}</p>";
        echo "<p>角色：{$user['role']}</p>";
        echo "<p>密码哈希：<code>" . htmlspecialchars($user['password']) . "</code></p>";
        
        // 测试不同的密码组合
        echo "<h2>密码验证测试</h2>";
        echo "<form method='post' action=''>";
        echo "<div style='margin: 10px 0;'>";
        echo "<label>输入密码：<input type='password' name='test_password' style='padding: 5px; width: 300px;'></label>";
        echo "</div>";
        echo "<button type='submit' name='test' style='padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;'>测试验证</button>";
        echo "</form>";
        
        if (isset($_POST['test'])) {
            $testPassword = $_POST['test_password'] ?? '';
            
            echo "<h3>测试结果</h3>";
            
            // 方法 1: 不加盐验证（兼容现有数据库）
            $method1 = password_verify($testPassword, $user['password']);
            echo "<p>方法 1 (不加盐): " . ($method1 ? "<strong style='color: green;'>✓ 通过</strong>" : "<strong style='color: red;'>✗ 失败</strong>") . "</p>";
            
            // 方法 2: 加盐验证
            $method2 = password_verify($testPassword . PASSWORD_SALT, $user['password']);
            echo "<p>方法 2 (加盐): " . ($method2 ? "<strong style='color: green;'>✓ 通过</strong>" : "<strong style='color: red;'>✗ 失败</strong>") . "</p>";
            
            // 方法 3: 使用 password_verify_custom 函数
            $method3 = password_verify_custom($testPassword, $user['password']);
            echo "<p>方法 3 (password_verify_custom): " . ($method3 ? "<strong style='color: green;'>✓ 通过</strong>" : "<strong style='color: red;'>✗ 失败</strong>") . "</p>";
            
            // 显示测试的密码（部分）
            if ($testPassword) {
                echo "<p>测试的密码：<code>" . htmlspecialchars(substr($testPassword, 0, 10)) . (strlen($testPassword) > 10 ? '...' : '') . "</code></p>";
                echo "<p>密码长度：" . strlen($testPassword) . "</p>";
            }
            
            // 显示密码哈希信息
            echo "<p>哈希格式检测：" . (strpos($user['password'], '$2y$') === 0 ? "<strong style='color: green;'>✓ bcrypt 格式正确</strong>" : "<strong style='color: red;'>✗ 格式错误</strong>") . "</p>";
            
            if ($method1) {
                echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; margin: 10px 0;'>";
                echo "<h4 style='color: green;'>✓ 密码正确！系统应该使用不加盐的方式验证。</h4>";
                echo "</div>";
            }
        }
        
        // 显示密码哈希的详细信息
        echo "<h3>密码哈希分析</h3>";
        echo "<ul>";
        echo "<li>哈希长度：" . strlen($user['password']) . "</li>";
        echo "<li>哈希前缀：" . substr($user['password'], 0, 6) . "</li>";
        echo "<li>哈希算法：bcrypt (如果前缀是 \$2y\$)</li>";
        echo "</ul>";
        
    } else {
        echo "<p style='color: red;'>✗ 未找到秘书长用户</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ 错误：" . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='/test_full_login.php'>返回完整登录测试</a></p>";
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1 { color: #333; }
    h2 { color: #666; margin-top: 30px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
    h3 { color: #555; margin-top: 20px; }
    code { background: #f5f5f5; padding: 2px 5px; border-radius: 3px; }
    ul { line-height: 1.8; }
</style>
