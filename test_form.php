<?php
/**
 * 测试登录表单渲染
 */

// 开启错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/config.php';
require_once 'core/functions.php';

// 启动会话
session_start();

// 模拟错误
$error = '这是一个测试错误信息';

// 直接渲染登录表单
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>测试登录表单</title>
    <style>
        body { 
            background-color: #f5f5f5; 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .login-container { max-width: 400px; margin: 100px auto; }
        .login-card { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .login-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .error-message {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 12px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .form-control {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }
        .btn {
            display: inline-block;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: center;
            text-decoration: none;
            vertical-align: middle;
            cursor: pointer;
            user-select: none;
            background-color: #0d6efd;
            color: white;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            border-radius: 0.25rem;
            border: none;
            width: 100%;
        }
        .mb-3 { margin-bottom: 1rem !important; }
        .form-label { margin-bottom: 0.5rem; display: block; }
        .form-check { display: flex; align-items: center; margin-bottom: 1rem; }
        .form-check-input { margin-right: 0.5rem; }
        .text-center { text-align: center; }
        .text-muted { color: #6c757d; }
        .mt-3 { margin-top: 1rem !important; }
        .p-4 { padding: 1.5rem !important; }
        .py-4 { padding-top: 1.5rem !important; padding-bottom: 1.5rem !important; }
        .card { position: relative; display: flex; flex-direction: column; background-color: #fff; border: 1px solid rgba(0,0,0,.125); border-radius: 0.25rem; }
        .card-header { padding: 0.5rem 1rem; margin-bottom: 0; background-color: rgba(0,0,0,.03); border-bottom: 1px solid rgba(0,0,0,.125); }
        .card-body { flex: 1 1 auto; }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-align: center; }
        h3 { margin: 0; font-size: 1.75rem; }
        small { font-size: 0.875em; font-weight: 400; }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="card login-card">
                <div class="card-header login-header py-4">
                    <h3 class="mb-0">测试登录</h3>
                    <small>后台管理系统</small>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($error)): ?>
                    <div class="error-message">
                        <strong>错误:</strong> <?php echo htmlspecialchars($error); ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">用户名</label>
                            <input type="text" class="form-control" id="username" name="username" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">密码</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">记住登录</label>
                        </div>
                        <button type="submit" class="btn">登录</button>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <small class="text-muted">使用数据库用户账号登录（秘书长拥有管理员权限）</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
