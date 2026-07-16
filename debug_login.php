<?php
/**
 * 登录调试页面
 * 用于调试登录问题
 */

require_once 'config/config.php';
require_once 'core/functions.php';

echo "<h1>登录功能调试</h1>";
echo "<hr>";

// 测试数据库连接
echo "<h2>1. 数据库连接</h2>";
try {
    $db = getDbConnection();
    if ($db) {
        echo "<p style='color: green;'>✓ 数据库连接成功</p>";
    } else {
        echo "<p style='color: red;'>✗ 数据库连接失败</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ 数据库连接异常：" . $e->getMessage() . "</p>";
}

// 测试获取秘书长用户
echo "<h2>2. 秘书长用户信息</h2>";
try {
    $db = getDbConnection();
    $sql = "SELECT id, username, role, country_id FROM users WHERE role = 'secretary_general'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $secretaryGenerals = $stmt->fetchAll();
    
    if (count($secretaryGenerals) > 0) {
        echo "<p style='color: green;'>✓ 找到 " . count($secretaryGenerals) . " 个秘书长用户</p>";
        foreach ($secretaryGenerals as $user) {
            echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0;'>";
            echo "<p><strong>用户名:</strong> {$user['username']}</p>";
            echo "<p><strong>角色:</strong> {$user['role']}</p>";
            echo "<p><strong>国家 ID:</strong> {$user['country_id']}</p>";
            echo "<p><strong>用户 ID:</strong> {$user['id']}</p>";
            echo "</div>";
        }
    } else {
        echo "<p style='color: orange;'>⚠ 未找到秘书长用户</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ 查询失败：" . $e->getMessage() . "</p>";
}

// 测试 AdminUserModel
echo "<h2>3. AdminUserModel 测试</h2>";
try {
    $model = new AdminUserModel();
    echo "<p style='color: green;'>✓ AdminUserModel 创建成功</p>";
    
    // 测试获取用户
    $testUser = $model->getUserByUsername('LouieMAIN');
    if ($testUser) {
        echo "<p style='color: green;'>✓ 成功获取用户 LouieMAIN</p>";
        echo "<pre>";
        print_r($testUser);
        echo "</pre>";
        
        // 测试密码验证（使用一个错误的密码测试）
        echo "<p><strong>密码哈希:</strong> " . substr($testUser['password'], 0, 20) . "...</p>";
        echo "<p><strong>密码哈希格式:</strong> " . (strpos($testUser['password'], '$2y$') === 0 ? "正确 (bcrypt)" : "错误") . "</p>";
    } else {
        echo "<p style='color: red;'>✗ 无法获取用户 LouieMAIN</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ AdminUserModel 测试失败：" . $e->getMessage() . "</p>";
}

// 测试权限配置
echo "<h2>4. 权限配置测试</h2>";
try {
    $model = new AdminUserModel();
    
    $roles = ['super_admin', 'secretary_general', 'permanent_member', 'diplomat', 'observer'];
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>角色</th><th>articles.view</th><th>statistics.view</th><th>contributions.view</th><th>settings.view</th></tr>";
    
    foreach ($roles as $role) {
        $hasArticlesView = $model->hasPermission($role, 'articles', 'view') ? '✓' : '✗';
        $hasStatsView = $model->hasPermission($role, 'statistics', 'view') ? '✓' : '✗';
        $hasContribView = $model->hasPermission($role, 'contributions', 'view') ? '✓' : '✗';
        $hasSettingsView = $model->hasPermission($role, 'settings', 'view') ? '✓' : '✗';
        
        echo "<tr>";
        echo "<td>{$role}</td>";
        echo "<td>{$hasArticlesView}</td>";
        echo "<td>{$hasStatsView}</td>";
        echo "<td>{$hasContribView}</td>";
        echo "<td>{$hasSettingsView}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ 权限测试失败：" . $e->getMessage() . "</p>";
}

// 测试会话
echo "<h2>5. 会话测试</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p style='color: green;'>✓ 会话已启动</p>";
    echo "<p><strong>SESSION 变量:</strong></p>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
} else {
    echo "<p style='color: orange;'>⚠ 会话未启动</p>";
}

// 测试密码验证函数
echo "<h2>6. 密码验证函数测试</h2>";
try {
    $testPassword = 'test_password';
    $hashed = password_hash($testPassword . PASSWORD_SALT, PASSWORD_DEFAULT);
    $verified = password_verify($testPassword . PASSWORD_SALT, $hashed);
    
    echo "<p>测试密码：<code>{$testPassword}</code></p>";
    echo "<p>哈希结果：<code>" . substr($hashed, 0, 30) . "...</code></p>";
    echo "<p>验证结果：" . ($verified ? "✓ 成功" : "✗ 失败") . "</p>";
    
    // 测试 password_verify_custom 函数
    if (function_exists('password_verify_custom')) {
        echo "<p style='color: green;'>✓ password_verify_custom 函数存在</p>";
    } else {
        echo "<p style='color: red;'>✗ password_verify_custom 函数不存在</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ 密码验证测试失败：" . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>调试建议</h2>";
echo "<ol>";
echo "<li>检查服务器错误日志（通常在 error_log 文件中）</li>";
echo "<li>确保 PHP 的 error_log 功能已启用</li>";
echo "<li>尝试使用秘书长账号 <strong>LouieMAIN</strong> 登录</li>";
echo "<li>如果登录失败，请查看浏览器控制台是否有 JavaScript 错误</li>";
echo "</ol>";

echo "<p><a href='/admin/login' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>前往登录页面</a></p>";
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1 { color: #333; }
    h2 { color: #666; margin-top: 30px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
    table { border-collapse: collapse; margin: 10px 0; width: 100%; }
    th { background-color: #f0f0f0; text-align: left; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    code { background: #f5f5f5; padding: 2px 5px; border-radius: 3px; }
</style>
