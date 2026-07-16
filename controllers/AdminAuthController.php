<?php
/**
 * 后台认证控制器
 */
class AdminAuthController {
    private $adminUserModel;
    
    public function __construct() {
        // 确保会话已启动（如果还没有）
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $this->adminUserModel = new AdminUserModel();
    }
    
    /**
     * 后台登录页面
     */
    public function login() {
        // 开启错误显示（调试用）
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        // 如果已登录，跳转到后台首页
        try {
            if ($this->isLoggedIn()) {
                header('Location: /admin/dashboard');
                exit;
            }
        } catch (Exception $e) {
            error_log("检查登录状态失败：" . $e->getMessage());
        }
        
        // 初始化错误信息
        $error = '';
        
        // 处理登录表单提交
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $error = $this->handleLogin();
            } catch (Exception $e) {
                error_log("登录处理异常：" . $e->getMessage());
                error_log("堆栈跟踪：" . $e->getTraceAsString());
                $error = '登录失败：' . $e->getMessage();
            }
        }
        
        // 显示登录页面
        $this->renderLoginForm($error);
    }
    
    /**
     * 处理登录逻辑
     */
    private function handleLogin() {
        $username = safe_input($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // 调试日志
        error_log("登录尝试 - 用户名：{$username}");
        
        // 验证输入
        if (empty($username) || empty($password)) {
            error_log("登录失败 - 用户名或密码为空");
            return '请输入用户名和密码';
        }
        
        try {
            // 验证用户
            $user = $this->authenticateUser($username, $password);
            
            if ($user) {
                // 登录成功
                error_log("登录成功 - 用户：{$username}");
                $this->createSession($user, $remember);
                
                // 记录操作日志
                log_operation('login', '后台登录', '用户登录成功');
                
                // 跳转到后台首页
                header('Location: /admin/dashboard');
                exit;
            } else {
                // 登录失败
                error_log("登录失败 - 用户名：{$username}");
                return '用户名或密码错误';
            }
        } catch (Exception $e) {
            error_log("登录验证异常：" . $e->getMessage());
            error_log("堆栈跟踪：" . $e->getTraceAsString());
            throw $e; // 重新抛出异常让 login() 方法处理
        }
    }
    
    /**
     * 用户认证
     */
    private function authenticateUser($username, $password) {
        // 首先尝试从 users 表验证（秘书长等用户）
        $user = $this->adminUserModel->getUserByUsername($username);
        
        error_log("查询用户结果：" . ($user ? "找到用户" : "未找到用户"));
        if ($user) {
            error_log("用户角色：" . $user['role']);
        }
        
        // 使用不加盐的方式验证密码（兼容现有数据库）
        if ($user && $this->adminUserModel->verifyPassword($password, $user['password'], false)) {
            error_log("密码验证通过");
            // 秘书长角色相当于 admin 管理员
            if ($user['role'] === 'secretary_general') {
                return [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => 'super_admin',
                    'source' => 'users'
                ];
            }
            // 外交官和常任理事国可以登录后台（基础权限）
            if (in_array($user['role'], ['permanent_member', 'diplomat'])) {
                return [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'country_id' => $user['country_id'] ?? null,
                    'source' => 'users'
                ];
            }
        }
        
        // 如果没有找到用户或密码错误，返回 false
        error_log("认证失败 - 用户名：{$username}");
        return false;
    }
    
    /**
     * 创建会话
     */
    private function createSession($user, $remember = false) {
        $_SESSION['admin_user_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_role'] = $user['role'];
        $_SESSION['admin_login_time'] = time();
        
        if ($remember) {
            // 设置记住登录的cookie
            $token = generate_random_string(32);
            setcookie('admin_remember_token', $token, time() + 86400 * 30, '/');
        }
    }
    
    /**
     * 检查是否已登录
     */
    private function isLoggedIn() {
        return isset($_SESSION['admin_user_id']) && 
               isset($_SESSION['admin_login_time']) &&
               (time() - $_SESSION['admin_login_time']) < SESSION_TIMEOUT;
    }
    
    /**
     * 渲染登录表单
     */
    private function renderLoginForm($error = '') {
        ?>
        <!DOCTYPE html>
        <html lang="zh-CN">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>后台登录 - <?php echo SITE_NAME; ?></title>
            <link href="<?php echo ASSET_URL; ?>/css/bootstrap.min.css" rel="stylesheet">
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
            </style>
        </head>
        <body>
            <div class="container">
                <div class="login-container">
                    <div class="card login-card">
                        <div class="card-header login-header text-center py-4">
                            <h3 class="mb-0"><?php echo SITE_NAME; ?></h3>
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
                                <button type="submit" class="btn btn-primary w-100">登录</button>
                            </form>
                            
                            <div class="mt-3 text-center">
                                <small class="text-muted">使用数据库用户账号登录（秘书长拥有管理员权限）</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <script src="<?php echo ASSET_URL; ?>/js/bootstrap.min.js"></script>
        </body>
        </html>
        <?php
    }
}
?>