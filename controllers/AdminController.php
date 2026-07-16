<?php
/**
 * 管理后台主控制器
 */
class AdminController {
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
        
        // 检查会话超时
        if (time() - $_SESSION['admin_login_time'] > SESSION_TIMEOUT) {
            session_destroy();
            header('Location: /admin/login');
            exit;
        }
        
        // 更新最后活动时间
        $_SESSION['admin_last_activity'] = time();
    }
    
    /**
     * 后台仪表板
     */
    public function dashboard() {
        // 检查权限（秘书长 super_admin 和所有有 statistics.view 权限的用户都可以访问）
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'statistics', 'view', $userId)) {
            $this->showError('权限不足');
            return;
        }
        
        // 获取统计数据
        $stats = $this->getDashboardStats();
        
        // 获取最新文章
        $latestArticles = $this->articleModel->getAll(['article_status' => 'published'], 'article_published_at DESC', 10);
        
        // 获取待审核投稿
        $pendingContributions = $this->articleModel->getContributions(null, 'pending', 1, 10);
        
        $this->renderAdmin('dashboard', [
            'stats' => $stats,
            'latestArticles' => $latestArticles,
            'pendingContributions' => $pendingContributions
        ]);
    }
    
    /**
     * 获取仪表板统计数据
     */
    private function getDashboardStats() {
        // 文章总数
        $totalArticles = $this->articleModel->count(['article_status' => 'published']);
        
        // 今日新增文章
        $todayArticles = $this->articleModel->count([
            'article_status' => 'published',
            'DATE(article_published_at)' => date('Y-m-d')
        ]);
        
        // 总阅读量 - 使用 Model 方法
        $totalReads = $this->articleModel->getTotalReads();
        
        // 总点赞量 - 使用 Model 方法
        $totalLikes = $this->articleModel->getTotalLikes();
        
        // 待审核投稿
        $pendingContributions = $this->articleModel->count([
            'article_status' => 'pending',
            'article_source_type' => 'user_contribution'
        ]);
        
        // 推送内容
        $pushArticles = $this->articleModel->count(['article_source_type' => 'subsite_push']);
        
        return [
            'total_articles' => $totalArticles,
            'today_articles' => $todayArticles,
            'total_reads' => $totalReads,
            'total_likes' => $totalLikes,
            'pending_contributions' => $pendingContributions,
            'push_articles' => $pushArticles
        ];
    }
    
    /**
     * 渲染后台视图
     */
    private function renderAdmin($view, $data = []) {
        extract($data);
        
        // 加载后台头部
        require_once VIEW_PATH . '/admin/layouts/header.php';
        
        // 加载后台侧边栏
        require_once VIEW_PATH . '/admin/layouts/sidebar.php';
        
        // 加载主体内容
        $viewFile = VIEW_PATH . '/admin/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "<div class='alert alert-danger'>视图文件不存在: {$viewFile}</div>";
        }
        
        // 加载后台底部
        require_once VIEW_PATH . '/admin/layouts/footer.php';
    }
    
    /**
     * 显示错误信息
     */
    private function showError($message) {
        echo "<div class='alert alert-danger'>{$message}</div>";
    }
}
?>