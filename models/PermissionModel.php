<?php
/**
 * 权限授权模型 - 用于秘书长授予用户额外权限
 */
class PermissionModel extends BaseModel {
    protected $table = TABLE_PERMISSIONS;

    /**
     * 授予用户权限
     */
    public function grantPermission($userId, $module, $action, $grantedBy) {
        $data = [
            'user_id' => $userId,
            'module' => $module,
            'action' => $action,
            'granted_by' => $grantedBy,
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $this->insert($data);
    }

    /**
     * 撤销用户权限
     */
    public function revokePermission($userId, $module, $action) {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ? AND module = ? AND action = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $module, $action]);
    }

    /**
     * 获取用户的所有权限
     */
    public function getUserPermissions($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * 检查用户是否有特定权限
     */
    public function hasPermission($userId, $module, $action) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE user_id = ? AND module = ? AND action = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $module, $action]);
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }

    /**
     * 获取用户的所有权限（简化格式）
     */
    public function getUserPermissionStrings($userId) {
        $permissions = $this->getUserPermissions($userId);
        $result = [];
        foreach ($permissions as $p) {
            $result[] = $p['module'] . '.' . $p['action'];
        }
        return $result;
    }

    /**
     * 撤销用户所有权限
     */
    public function revokeAllPermissions($userId) {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
}
?>
