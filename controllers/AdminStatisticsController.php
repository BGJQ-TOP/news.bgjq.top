<?php
/**
 * 后台统计管理控制器
 */
class AdminStatisticsController {
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
     * 统计页面
     */
    public function index() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'statistics', 'view', $userId)) {
            $this->showError('权限不足');
            return;
        }
        
        // 获取统计数据 - 使用 Model 方法
        $totalArticles = $this->articleModel->count();
        $publishedArticles = $this->articleModel->count(['article_status' => 'published']);
        $pendingArticles = $this->articleModel->count(['article_status' => 'pending']);
        $totalCategories = $this->categoryModel->count();
        
        // 使用新增的统计方法
        $totalReads = $this->articleModel->getTotalReads();
        $totalLikes = $this->articleModel->getTotalLikes();
        $trendData = $this->articleModel->getPublishTrend(7);
        $popularArticles = $this->articleModel->getPopularArticles(10);
        $categoryDistribution = $this->categoryModel->getCategoryDistribution();
        
        // 获取用户总数
        $userModel = new UserModel();
        $totalUsers = $userModel->count();
        
        $this->renderAdmin('statistics/index', [
            'totalArticles' => $totalArticles,
            'publishedArticles' => $publishedArticles,
            'pendingArticles' => $pendingArticles,
            'totalCategories' => $totalCategories,
            'totalUsers' => $totalUsers,
            'totalReads' => $totalReads,
            'totalLikes' => $totalLikes,
            'trendData' => $trendData,
            'popularArticles' => $popularArticles,
            'categoryDistribution' => $categoryDistribution,
            'pageTitle' => '数据统计'
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
