<?php
/**
 * 后台投稿管理控制器
 */
class AdminContributionController {
    private $articleModel;
    private $categoryModel;
    private $adminUserModel;
    
    public function __construct() {
        $this->checkAuth();
        $this->articleModel = new ArticleModel();
        $this->categoryModel = new CategoryModel();
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
     * 投稿列表页
     */
    public function index() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'contributions', 'view', $userId)) {
            $this->showError('权限不足');
            return;
        }
        
        // 获取查询参数
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $status = isset($_GET['status']) ? $_GET['status'] : 'pending';
        
        // 获取投稿列表
        $contributions = $this->articleModel->getContributions(null, $status, $page, 20);
        
        $this->renderAdmin('contributions/index', [
            'contributions' => $contributions,
            'currentStatus' => $status,
            'pageTitle' => '投稿审核'
        ]);
    }
    
    /**
     * 审核通过
     */
    public function approve() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'contributions', 'publish', $userId)) {
            $this->jsonResponse(false, '权限不足');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, '请求方法错误');
            return;
        }
        
        $articleId = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if (!$articleId) {
            $this->jsonResponse(false, '文章 ID 不能为空');
            return;
        }
        
        try {
            // 更新文章状态为已发布
            $data = [
                'article_status' => 'published',
                'article_published_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $this->articleModel->update($articleId, $data);
            
            if ($result) {
                log_operation('contribution_approve', '投稿审核', '通过投稿 ID: ' . $articleId);
                
                // 提交 IndexNow
                $article = $this->articleModel->getById($articleId);
                if ($article) {
                    $indexNow = new IndexNowService();
                    $indexNow->submitUrl(SITE_URL . '/a/' . $article['article_slug'], 'contribution_approve');
                }
                
                $this->jsonResponse(true, '审核通过');
            } else {
                $this->jsonResponse(false, '审核失败');
            }
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }
    
    /**
     * 审核拒绝
     */
    public function reject() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'contributions', 'reject', $userId)) {
            $this->jsonResponse(false, '权限不足');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, '请求方法错误');
            return;
        }
        
        $articleId = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
        
        if (!$articleId) {
            $this->jsonResponse(false, '文章 ID 不能为空');
            return;
        }
        
        try {
            // 更新文章状态为已拒绝
            $data = [
                'article_status' => 'rejected',
                'article_reject_reason' => $reason
            ];
            
            $result = $this->articleModel->update($articleId, $data);
            
            if ($result) {
                log_operation('contribution_reject', '投稿审核', '拒绝投稿 ID: ' . $articleId);
                $this->jsonResponse(true, '已拒绝');
            } else {
                $this->jsonResponse(false, '操作失败');
            }
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }
    
    /**
     * 删除投稿
     */
    public function delete() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'contributions', 'delete', $userId)) {
            $this->jsonResponse(false, '权限不足');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, '请求方法错误');
            return;
        }
        
        $articleId = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if (!$articleId) {
            $this->jsonResponse(false, '文章 ID 不能为空');
            return;
        }
        
        try {
            $result = $this->articleModel->delete($articleId);
            
            if ($result) {
                log_operation('contribution_delete', '投稿删除', '删除投稿 ID: ' . $articleId);
                $this->jsonResponse(true, '删除成功');
            } else {
                $this->jsonResponse(false, '删除失败');
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
