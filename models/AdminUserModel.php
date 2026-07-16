<?php
/**
 * 管理员模型类
 */
class AdminUserModel extends BaseModel {
    protected $table = TABLE_ADMIN_USERS;
    protected $usersTable = TABLE_USERS;
    private $permissionModel;
    
    public function __construct() {
        parent::__construct();
        $this->permissionModel = new PermissionModel();
    }
    
    /**
     * 根据用户名获取管理员（从 admin_users 表）
     */
    public function getByUsername($username) {
        $sql = "SELECT * FROM {$this->table} WHERE username = ? AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    /**
     * 根据用户名获取用户（从 users 表，用于秘书长登录）
     */
    public function getUserByUsername($username) {
        $sql = "SELECT id, username, password, role, country_id FROM {$this->usersTable} WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    /**
     * 验证密码（支持加盐和不加盐两种方式）
     */
    public function verifyPassword($password, $hash, $useSalt = true) {
        // 如果密码哈希已经包含盐（旧数据），直接验证
        // 否则使用盐验证（新数据）
        if ($useSalt && defined('PASSWORD_SALT')) {
            return password_verify($password . PASSWORD_SALT, $hash);
        } else {
            // 不加盐验证（兼容现有数据库）
            return password_verify($password, $hash);
        }
    }
    
    /**
     * 更新最后登录信息
     */
    public function updateLoginInfo($userId, $ip) {
        $sql = "UPDATE {$this->table} SET last_login_at = NOW(), last_login_ip = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$ip, $userId]);
    }
    
    /**
     * 获取所有管理员（分页）
     */
    public function getAllAdmins($page = 1, $pageSize = 20) {
        return $this->paginate([], 'created_at DESC', $page, $pageSize);
    }
    
    /**
     * 检查用户名是否已存在
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE username = ?";
        $params = [$username];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }
    
    /**
     * 创建管理员
     */
    public function createAdmin($data) {
        $required = ['username', 'password', 'real_name', 'role'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("字段 {$field} 为必填项");
            }
        }
        
        if ($this->usernameExists($data['username'])) {
            throw new Exception("用户名已存在");
        }
        
        $data['password'] = password_hash_custom($data['password']);
        
        return $this->insert($data);
    }
    
    /**
     * 更新管理员
     */
    public function updateAdmin($id, $data) {
        if (isset($data['username']) && $this->usernameExists($data['username'], $id)) {
            throw new Exception("用户名已存在");
        }
        
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash_custom($data['password']);
        } else {
            unset($data['password']);
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * 获取角色权限配置
     */
    public function getRolePermissions($role) {
        $permissions = [
            'super_admin' => [
                'articles' => ['view', 'create', 'edit', 'delete', 'publish'],
                'categories' => ['view', 'create', 'edit', 'delete'],
                'contributions' => ['view', 'review', 'publish', 'reject'],
                'users' => ['view', 'create', 'edit', 'delete'],
                'settings' => ['view', 'edit'],
                'statistics' => ['view']
            ],
            'secretary_general' => [
                'articles' => ['view', 'create', 'edit', 'delete', 'publish'],
                'categories' => ['view', 'create', 'edit', 'delete'],
                'contributions' => ['view', 'review', 'publish', 'reject'],
                'users' => ['view', 'create', 'edit', 'delete'],
                'settings' => ['view', 'edit'],
                'statistics' => ['view']
            ],
            'permanent_member' => [
                'articles' => ['view', 'create', 'edit'],
                'categories' => ['view'],
                'contributions' => ['view'],
                'statistics' => ['view']
            ],
            'diplomat' => [
                'articles' => ['view', 'create', 'edit'],
                'categories' => ['view'],
                'contributions' => ['view', 'review'],
                'statistics' => ['view']
            ],
            'observer' => [
                'articles' => ['view'],
                'contributions' => ['view']
            ],
            'peacekeeper' => [
                'articles' => ['view', 'create', 'edit'],
                'categories' => ['view'],
                'contributions' => ['view', 'review'],
                'statistics' => ['view']
            ],
            'category_editor' => [
                'articles' => ['view', 'create', 'edit'],
                'categories' => ['view'],
                'contributions' => ['view'],
                'statistics' => ['view']
            ],
            'content_reviewer' => [
                'contributions' => ['view', 'review', 'publish', 'reject'],
                'articles' => ['view'],
                'statistics' => ['view']
            ],
            'data_operator' => [
                'statistics' => ['view'],
                'articles' => ['view']
            ],
            'contribution_operator' => [
                'contributions' => ['view'],
                'articles' => ['view'],
                'statistics' => ['view']
            ]
        ];
        
        return $permissions[$role] ?? [];
    }
    
    /**
     * 检查权限
     */
    public function hasPermission($role, $module, $action, $userId = null) {
        $permissions = $this->getRolePermissions($role);
        if (isset($permissions[$module]) && in_array($action, $permissions[$module])) {
            return true;
        }
        
        if ($userId !== null) {
            return $this->permissionModel->hasPermission($userId, $module, $action);
        }
        
        return false;
    }
    
    /**
     * 获取用户的额外权限列表
     */
    public function getExtraPermissions($userId) {
        return $this->permissionModel->getUserPermissionStrings($userId);
    }
    
    /**
     * 授予用户额外权限
     */
    public function grantExtraPermission($userId, $module, $action, $grantedBy) {
        return $this->permissionModel->grantPermission($userId, $module, $action, $grantedBy);
    }
    
    /**
     * 撤销用户额外权限
     */
    public function revokeExtraPermission($userId, $module, $action) {
        return $this->permissionModel->revokePermission($userId, $module, $action);
    }
    
    /**
     * 撤销用户所有额外权限
     */
    public function revokeAllExtraPermissions($userId) {
        return $this->permissionModel->revokeAllPermissions($userId);
    }
}
?>