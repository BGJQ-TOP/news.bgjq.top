<?php
/**
 * 后台推送管理控制器
 */
class AdminPushController {
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
     * 推送管理页面
     */
    public function index() {
        // 检查权限（只有超级管理员可以访问）
        $role = $_SESSION['admin_role'] ?? '';
        if ($role !== 'super_admin') {
            $this->showError('权限不足');
            return;
        }
        
        // 获取推送文章列表
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $pushArticles = $this->articleModel->getPushArticles(null, $page, 20);
        
        $this->renderAdmin('push/index', [
            'pushArticles' => $pushArticles,
            'pageTitle' => '推送管理'
        ]);
    }
    
    /**
     * 添加推送
     */
    public function add() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        if ($role !== 'super_admin') {
            $this->jsonResponse(false, '权限不足');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, '请求方法错误');
            return;
        }
        
        $articleId = isset($_POST['article_id']) ? intval($_POST['article_id']) : 0;
        
        if (!$articleId) {
            $this->jsonResponse(false, '文章 ID 不能为空');
            return;
        }
        
        try {
            // 更新文章为推送类型
            $data = [
                'article_source_type' => 'subsite_push'
            ];
            
            $result = $this->articleModel->update($articleId, $data);
            
            if ($result) {
                log_operation('push_add', '推送管理', '添加推送文章 ID: ' . $articleId);
                $this->jsonResponse(true, '添加成功');
            } else {
                $this->jsonResponse(false, '添加失败');
            }
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }
    
    /**
     * 移除推送
     */
    public function remove() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        if ($role !== 'super_admin') {
            $this->jsonResponse(false, '权限不足');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, '请求方法错误');
            return;
        }
        
        $articleId = isset($_POST['article_id']) ? intval($_POST['article_id']) : 0;
        
        if (!$articleId) {
            $this->jsonResponse(false, '文章 ID 不能为空');
            return;
        }
        
        try {
            // 更新文章为普通类型
            $data = [
                'article_source_type' => 'normal'
            ];
            
            $result = $this->articleModel->update($articleId, $data);
            
            if ($result) {
                log_operation('push_remove', '推送管理', '移除推送文章 ID: ' . $articleId);
                $this->jsonResponse(true, '移除成功');
            } else {
                $this->jsonResponse(false, '移除失败');
            }
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
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
    
    /**
     * JSON 响应
     */
    private function jsonResponse($success, $message) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message
        ]);
        exit;
    }
}
