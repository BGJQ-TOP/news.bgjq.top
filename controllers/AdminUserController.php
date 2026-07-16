<?php
/**
 * 后台用户管理控制器
 */
class AdminUserController {
    private $articleModel;
    private $adminUserModel;
    
    public function __construct() {
        $this->checkAuth();
        $this->articleModel = new ArticleModel();
        $this->adminUserModel = new AdminUserModel();
    }
    
    /**
     * 权限检查
     */
    private function checkAuth() {
        if (!isset($_SESSION['admin_user_id']) || !isset($_SESSION['admin_login_time'])) {
            header('Location: /admin/login');
            exit;
        }
        
        if (time() - $_SESSION['admin_login_time'] > SESSION_TIMEOUT) {
            session_destroy();
            header('Location: /admin/login');
            exit;
        }
        
        $_SESSION['admin_last_activity'] = time();
    }
    
    /**
     * 用户列表页
     */
    public function index() {
        // 检查权限（只有超级管理员可以访问）
        $role = $_SESSION['admin_role'] ?? '';
        if ($role !== 'super_admin') {
            $this->showError('权限不足');
            return;
        }
        
        // 获取用户列表（从 users 表获取秘书长等用户）
        $db = getDbConnection();
        $sql = "SELECT u.*, c.name as country_name 
                FROM users u 
                LEFT JOIN countries c ON u.country_id = c.id 
                ORDER BY u.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll();
        
        $this->renderAdmin('users/index', [
            'users' => $users,
            'pageTitle' => '用户管理'
        ]);
    }
    
    /**
     * 渲染后台视图
     */
    private function renderAdmin($view, $data = []) {
        extract($data);
        
        require_once VIEW_PATH . '/admin/layouts/header.php';
        require_once VIEW_PATH . '/admin/layouts/sidebar.php';
        
        $viewPath = VIEW_PATH . '/admin/' . str_replace('.', '/', $view) . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            throw new Exception("视图文件不存在：{$viewPath}");
        }
        
        require_once VIEW_PATH . '/admin/layouts/footer.php';
    }
    
    /**
     * 显示错误页面
     */
    private function showError($message) {
        $this->renderAdmin('error', [
            'message' => $message
        ]);
    }
}
