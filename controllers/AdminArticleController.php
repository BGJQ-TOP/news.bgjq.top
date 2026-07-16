<?php
/**
 * 后台文章管理控制器
 */
class AdminArticleController {
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
     * 文章列表页
     */
    public function index() {
        // 检查权限（秘书长 super_admin 和所有有 articles.view 权限的用户都可以访问）
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'articles', 'view', $userId)) {
            $this->showError('权限不足');
            return;
        }
        
        // 获取查询参数
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $categoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $keyword = isset($_GET['keyword']) ? safe_input($_GET['keyword']) : '';
        
        // 构建查询条件
        $conditions = [];
        if ($categoryId) {
            $conditions['article_category_id'] = $categoryId;
        }
        if ($status) {
            $conditions['article_status'] = $status;
        }
        
        // 获取文章列表
        $articles = $this->articleModel->paginate($conditions, 'article_published_at DESC', $page, 20);
        
        // 获取栏目列表
        $categories = $this->categoryModel->getActiveCategories();
        
        $this->renderAdmin('articles/index', [
            'articles' => $articles,
            'categories' => $categories,
            'currentCategory' => $categoryId,
            'currentStatus' => $status,
            'keyword' => $keyword,
            'pageTitle' => '文章管理'
        ]);
    }
    
    /**
     * 创建文章页面
     */
    public function create() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'articles', 'create', $userId)) {
            $this->showError('权限不足');
            return;
        }
        
        // 获取栏目列表
        $categories = $this->categoryModel->getActiveCategories();
        
        $this->renderAdmin('articles/create', [
            'categories' => $categories,
            'pageTitle' => '发布新文章'
        ]);
    }
    
    /**
     * 保存文章
     */
    public function store() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'articles', 'create', $userId)) {
            $this->jsonResponse(false, '权限不足');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, '请求方法错误');
            return;
        }
        
        // 获取并验证数据
        $data = $this->validateArticleData($_POST);
        
        if (!$data) {
            return;
        }
        
        try {
            // 生成 URL 别名
            $data['article_slug'] = generate_slug($data['article_title']);
            
            // 设置默认值
            $data['article_author_id'] = $_SESSION['admin_user_id'];
            $data['article_source_type'] = 'admin';
            $data['article_status'] = 'published';
            $data['article_published_at'] = date('Y-m-d H:i:s');
            
            // 插入文章
            $articleId = $this->articleModel->insert($data);
            
            if ($articleId) {
                // 记录操作日志
                log_operation('article_create', '文章发布', '发布文章：' . $data['article_title']);
                
                // 提交 IndexNow
                $indexNow = new IndexNowService();
                $indexNow->submitUrl(SITE_URL . '/a/' . $data['article_slug'], 'article_publish');
                
                $this->jsonResponse(true, '文章发布成功', ['id' => $articleId]);
            } else {
                $this->jsonResponse(false, '文章发布失败');
            }
            
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }
    
    /**
     * 编辑文章页面
     */
    public function edit() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'articles', 'edit', $userId)) {
            $this->showError('权限不足');
            return;
        }
        
        $articleId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if (!$articleId) {
            $this->showError('文章ID不能为空');
            return;
        }
        
        // 获取文章信息
        $article = $this->articleModel->getById($articleId);
        
        if (!$article) {
            $this->showError('文章不存在');
            return;
        }
        
        // 获取栏目列表
        $categories = $this->categoryModel->getActiveCategories();
        
        $this->renderAdmin('articles/edit', [
            'article' => $article,
            'categories' => $categories,
            'pageTitle' => '编辑文章'
        ]);
    }
    
    /**
     * 更新文章
     */
    public function update() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'articles', 'edit', $userId)) {
            $this->jsonResponse(false, '权限不足');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, '请求方法错误');
            return;
        }
        
        $articleId = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if (!$articleId) {
            $this->jsonResponse(false, '文章ID不能为空');
            return;
        }
        
        // 获取并验证数据
        $data = $this->validateArticleData($_POST);
        
        if (!$data) {
            return;
        }
        
        try {
            // 更新文章
            $result = $this->articleModel->update($articleId, $data);
            
            if ($result) {
                // 记录操作日志
                log_operation('article_update', '文章编辑', '编辑文章ID: ' . $articleId);
                
                $this->jsonResponse(true, '文章更新成功');
            } else {
                $this->jsonResponse(false, '文章更新失败');
            }
            
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }
    
    /**
     * 删除文章
     */
    public function delete() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'articles', 'delete', $userId)) {
            $this->jsonResponse(false, '权限不足');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(false, '请求方法错误');
            return;
        }
        
        $articleId = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if (!$articleId) {
            $this->jsonResponse(false, '文章ID不能为空');
            return;
        }
        
        try {
            // 删除文章
            $result = $this->articleModel->delete($articleId);
            
            if ($result) {
                // 记录操作日志
                log_operation('article_delete', '文章删除', '删除文章ID: ' . $articleId);
                
                $this->jsonResponse(true, '文章删除成功');
            } else {
                $this->jsonResponse(false, '文章删除失败');
            }
            
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }
    
    /**
     * 验证文章数据
     */
    private function validateArticleData($postData) {
        $data = [];
        
        // 验证标题
        $title = trim($postData['title'] ?? '');
        if (empty($title)) {
            $this->jsonResponse(false, '文章标题不能为空');
            return false;
        }
        if (mb_strlen($title, 'UTF-8') > 200) {
            $this->jsonResponse(false, '文章标题不能超过 200 个字符');
            return false;
        }
        $data['article_title'] = $title;
        
        // 验证栏目
        $categoryId = intval($postData['category_id'] ?? 0);
        if ($categoryId <= 0) {
            $this->jsonResponse(false, '请选择文章栏目');
            return false;
        }
        $data['article_category_id'] = $categoryId;
        
        // 验证内容
        $content = trim($postData['content'] ?? '');
        if (empty($content)) {
            $this->jsonResponse(false, '文章内容不能为空');
            return false;
        }
        $data['article_content'] = $content;
        
        // 可选字段
        if (!empty($postData['cover_image'])) {
            $data['article_cover_image'] = safe_input($postData['cover_image']);
        }
        
        if (!empty($postData['seo_title'])) {
            $data['article_seo_title'] = safe_input($postData['seo_title']);
        }
        
        if (!empty($postData['seo_keywords'])) {
            $data['article_seo_keywords'] = safe_input($postData['seo_keywords']);
        }
        
        if (!empty($postData['seo_description'])) {
            $data['article_seo_description'] = safe_input($postData['seo_description']);
        }
        
        // 状态设置
        $data['article_is_headline'] = isset($postData['is_headline']) ? 1 : 0;
        $data['article_is_featured'] = isset($postData['is_featured']) ? 1 : 0;
        $data['article_is_top'] = isset($postData['is_top']) ? 1 : 0;
        
        return $data;
    }
    
    /**
     * 渲染后台视图
     */
    private function renderAdmin($view, $data = []) {
        extract($data);
        
        require_once VIEW_PATH . '/admin/layouts/header.php';
        require_once VIEW_PATH . '/admin/layouts/sidebar.php';
        
        $viewFile = VIEW_PATH . '/admin/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "<div class='alert alert-danger'>视图文件不存在: {$viewFile}</div>";
        }
        
        require_once VIEW_PATH . '/admin/layouts/footer.php';
    }
    
    /**
     * 显示错误信息
     */
    private function showError($message) {
        echo "<div class='alert alert-danger'>{$message}</div>";
    }
    
    /**
     * JSON响应
     */
    private function jsonResponse($success, $message, $data = []) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>