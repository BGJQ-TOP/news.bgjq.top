<?php
/**
 * 登录功能测试脚本
 * 用于验证数据库用户登录功能
 */

require_once 'config/config.php';
require_once 'core/functions.php';

echo "<h1>登录功能测试</h1>";
echo "<hr>";

// 测试 1: 数据库连接
echo "<h2>1. 测试数据库连接</h2>";
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

// 测试 2: 获取用户数据
echo "<h2>2. 测试获取用户数据</h2>";
try {
    $db = getDbConnection();
    $sql = "SELECT id, username, role, country_id FROM users WHERE role = 'secretary_general'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $secretaryGenerals = $stmt->fetchAll();
    
    if (count($secretaryGenerals) > 0) {
        echo "<p style='color: green;'>✓ 找到 " . count($secretaryGenerals) . " 个秘书长用户:</p>";
        echo "<ul>";
        foreach ($secretaryGenerals as $user) {
            echo "<li>用户名：{$user['username']}, 角色：{$user['role']}, ID: {$user['id']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠ 未找到秘书长用户</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ 查询失败：" . $e->getMessage() . "</p>";
}

// 测试 3: 获取所有用户角色分布
echo "<h2>3. 用户角色分布统计</h2>";
try {
    $db = getDbConnection();
    $sql = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $roles = $stmt->fetchAll();
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>角色</th><th>数量</th></tr>";
    foreach ($roles as $role) {
        echo "<tr><td>{$role['role']}</td><td>{$role['count']}</td></tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ 统计失败：" . $e->getMessage() . "</p>";
}

// 测试 4: 测试密码验证
echo "<h2>4. 测试密码验证功能</h2>";
try {
    $adminUserModel = new AdminUserModel();
    
    // 获取第一个秘书长用户
    $db = getDbConnection();
    $sql = "SELECT * FROM users WHERE role = 'secretary_general' LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p>测试用户：{$user['username']}</p>";
        
        // 注意：这里不能测试具体密码，因为我们不知道明文密码
        // 只验证密码哈希格式
        if (strpos($user['password'], '$2y$') === 0) {
            echo "<p style='color: green;'>✓ 密码哈希格式正确 (使用 bcrypt)</p>";
        } else {
            echo "<p style='color: red;'>✗ 密码哈希格式可能不正确</p>";
        }
        
        echo "<p>用户信息:</p>";
        echo "<ul>";
        echo "<li>ID: {$user['id']}</li>";
        echo "<li>用户名：{$user['username']}</li>";
        echo "<li>角色：{$user['role']}</li>";
        echo "<li>国家 ID: {$user['country_id']}</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠ 未找到测试用户</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ 测试失败：" . $e->getMessage() . "</p>";
}

// 测试 5: AdminUserModel 权限测试
echo "<h2>5. 测试权限配置</h2>";
try {
    $adminUserModel = new AdminUserModel();
    
    $roles = ['super_admin', 'secretary_general', 'permanent_member', 'diplomat', 'observer'];
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>角色</th><th>articles.view</th><th>statistics.view</th><th>contributions.view</th></tr>";
    
    foreach ($roles as $role) {
        $hasArticlesView = $adminUserModel->hasPermission($role, 'articles', 'view') ? '✓' : '✗';
        $hasStatsView = $adminUserModel->hasPermission($role, 'statistics', 'view') ? '✓' : '✗';
        $hasContribView = $adminUserModel->hasPermission($role, 'contributions', 'view') ? '✓' : '✗';
        
        echo "<tr>";
        echo "<td>{$role}</td>";
        echo "<td>{$hasArticlesView}</td>";
        echo "<td>{$hasStatsView}</td>";
        echo "<td>{$hasContribView}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ 权限测试失败：" . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>测试完成！</strong></p>";
echo "<p>请使用秘书长账号访问 <a href='/admin/login'>/admin/login</a> 进行登录测试</p>";
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1 { color: #333; }
    h2 { color: #666; margin-top: 30px; }
    table { border-collapse: collapse; margin: 10px 0; }
    th { background-color: #f0f0f0; }
</style>
