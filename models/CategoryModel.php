<?php
/**
 * 栏目模型类
 */
class CategoryModel {
    protected $table = 'news_categories';
    protected $db;
    
    public function __construct() {
        require_once 'config/database.php';
        $this->db = getDbConnection();
    }
    
    /**
     * 获取所有启用的栏目
     */
    public function getActiveCategories() {
        $sql = "SELECT * FROM {$this->table} WHERE category_is_active = 1 ORDER BY category_sort_order ASC, id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * 根据编码获取栏目
     */
    public function getByCode($code) {
        $sql = "SELECT * FROM {$this->table} WHERE category_code = ? AND category_is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$code]);
        return $stmt->fetch();
    }
    
    /**
     * 根据别名获取栏目
     */
    public function getBySlug($slug) {
        $sql = "SELECT * FROM {$this->table} WHERE category_slug = ? AND category_is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
    
    /**
     * 根据 ID 获取栏目
     */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND category_is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * 获取所有栏目
     */
    public function getAllCategories() {
        $sql = "SELECT * FROM {$this->table} WHERE category_is_active = 1 ORDER BY category_sort_order ASC, id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * 获取栏目文章统计（不区分投稿，统一显示文章数量）
     */
    public function getCategoryStats() {
        $sql = "SELECT c.id, c.category_name as name, c.category_code as code, COUNT(n.id) as article_count 
                FROM {$this->table} c 
                LEFT JOIN news_articles n ON c.id = n.article_category_id AND n.article_status = 'published' 
                WHERE c.category_is_active = 1 
                GROUP BY c.id, c.category_name, c.category_code 
                ORDER BY c.category_sort_order ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * 统计栏目数量
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
    
    /**
     * 获取栏目文章分布统计
     */
    public function getCategoryDistribution() {
        $sql = "
            SELECT c.category_name, COUNT(a.id) as article_count
            FROM {$this->table} c
            LEFT JOIN news_articles a ON c.id = a.article_category_id
            GROUP BY c.id, c.category_name
            ORDER BY article_count DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
