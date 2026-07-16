<?php
/**
 * 8W社区用户模型
 */
class UserModel {
    protected $table = 'users';
    protected $db;
    
    public function __construct() {
        require_once dirname(__DIR__) . '/config/database.php';
        $this->db = getDbConnection();
    }
    
    /**
     * 根据用户名验证用户
     */
    public function verifyUser($username, $password) {
        $sql = "SELECT * FROM {$this->table} WHERE username = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return false;
        }
        
        if (password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * 根据ID获取用户
     */
    public function getById($id) {
        $sql = "SELECT id, username, game_id, country_id, role, jhtuid, level FROM {$this->table} WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * 根据用户名获取用户
     */
    public function getByUsername($username) {
        $sql = "SELECT id, username, game_id, country_id, role, jhtuid, level FROM {$this->table} WHERE username = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    /**
     * 检查用户是否为外交官(diplomat)
     */
    public function isDiplomat($userId) {
        $sql = "SELECT role FROM {$this->table} WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        return $user && $user['role'] === 'diplomat';
    }
    
    /**
     * 检查用户是否为外交官或更高权限
     */
    public function isDiplomatOrAbove($userId) {
        $sql = "SELECT role FROM {$this->table} WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return false;
        }
        
        return in_array($user['role'], ['secretary_general', 'permanent_member', 'diplomat']);
    }
    
    /**
     * 检查用户今日是否已发布外交公告
     */
    public function hasPublishedDiplomatAnnouncementToday($userId) {
        $sql = "SELECT COUNT(*) as count FROM news_articles 
                WHERE article_author_id = ? 
                AND article_source_type = 'diplomat_announcement'
                AND DATE(article_published_at) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
    
    /**
     * 获取用户所属邦国信息
     */
    public function getUserCountry($userId) {
        $sql = "SELECT c.id, c.name, c.flag_url, c.description 
                FROM countries c
                JOIN {$this->table} u ON u.country_id = c.id
                WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
    
    /**
     * 统计用户数量
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "$field = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'];
    }
}
?>