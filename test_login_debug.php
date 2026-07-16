<?php
/**
 * 登录测试脚本
 */
require_once 'config/config.php';
require_once 'core/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

header('Content-Type: application/json');

echo "=== 登录测试开始 ===\n\n";

echo "1. 测试数据库连接...\n";
$db = getDbConnection();
if ($db) {
    echo "   数据库连接成功\n";
} else {
    echo "   数据库连接失败\n";
    echo json_encode(['success' => false, 'message' => '数据库连接失败']);
    exit;
}

echo "2. 测试用户模型加载...\n";
require_once 'models/UserModel.php';
$userModel = new UserModel();
echo "   用户模型加载成功\n";

echo "3. 测试用户验证...\n";
$username = isset($_POST['username']) ? trim($_POST['username']) : 'LouieMAIN';
$password = isset($_POST['password']) ? $_POST['password'] : 'test';

echo "   测试用户名: $username\n";
$user = $userModel->verifyUser($username, $password);

if ($user) {
    echo "   用户验证成功! 用户ID: " . $user['id'] . ", 用户名: " . $user['username'] . "\n";
    echo "   角色: " . $user['role'] . "\n";
    
    $_SESSION['bgjq_user_id'] = $user['id'];
    $_SESSION['bgjq_username'] = $user['username'];
    $_SESSION['bgjq_role'] = $user['role'];
    
    echo json_encode([
        'success' => true, 
        'message' => '登录成功',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ]
    ]);
} else {
    echo "   用户验证失败\n";
    echo json_encode(['success' => false, 'message' => '用户名或密码错误']);
}

echo "\n=== 测试结束 ===\n";
