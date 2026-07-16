<?php
/**
 * 测试登录错误显示
 */

// 启动会话（在任何输出之前）
session_start();

// 加载必要的文件
require_once 'config/config.php';
require_once 'core/functions.php';
require_once 'core/BaseModel.php';
require_once 'models/AdminUserModel.php';
require_once 'controllers/AdminAuthController.php';

// 模拟 POST 登录
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST['username'] = 'LouieMAIN';
    $_POST['password'] = 'wrong_password'; // 错误的密码
}

// 创建控制器并调用 login 方法
$controller = new AdminAuthController();

// 使用输出缓冲来捕获任何提前的输出
ob_start();
$controller->login();
$output = ob_get_clean();

// 显示捕获的输出
echo $output;
?>
