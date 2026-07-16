<?php
/**
 * 后台栏目管理控制器
 */
class AdminCategoryController {
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
     * 栏目列表页
     */
    public function index() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'categories', 'view', $userId)) {
            $this->showError('权限不足');
            return;
        }
        
        // 获取栏目列表
        $categories = $this->categoryModel->getAllCategories();
        
        $this->renderAdmin('categories/index', [
            'categories' => $categories,
            'pageTitle' => '栏目管理'
        ]);
    }
    
    /**
     * 创建栏目页面
     */
    public function create() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'categories', 'create', $userId)) {
            $this->showError('权限不足');
            return;
        }
        
        $this->renderAdmin('categories/create', [
            'pageTitle' => '创建栏目'
        ]);
    }
    
    /**
     * 保存栏目
     */
    public function store() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'categories', 'create', $userId)) {
            $this->showError('权限不足');
            return;
        }
        
        // TODO: 实现保存逻辑
        
        header('Location: /admin/categories');
        exit;
    }
    
    /**
     * 编辑栏目页面
     */
    public function edit() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'categories', 'edit', $userId)) {
            $this->showError('权限不足');
            return;
        }
        
        // TODO: 实现编辑逻辑
        
        $this->renderAdmin('categories/edit', [
            'pageTitle' => '编辑栏目'
        ]);
    }
    
    /**
     * 更新栏目
     */
    public function update() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'categories', 'edit', $userId)) {
            $this->showError('权限不足');
            return;
        }
        
        // TODO: 实现更新逻辑
        
        header('Location: /admin/categories');
        exit;
    }
    
    /**
     * 删除栏目
     */
    public function delete() {
        // 检查权限
        $role = $_SESSION['admin_role'] ?? '';
        $userId = $_SESSION['admin_user_id'] ?? null;
        if (!$this->adminUserModel->hasPermission($role, 'categories', 'delete', $userId)) {
            $this->showError('权限不足');
            return;
        }
        
        // TODO: 实现删除逻辑
        
        header('Location: /admin/categories');
        exit;
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
