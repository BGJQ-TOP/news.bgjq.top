<?php
/**
 * 文章模型类
 */
class ArticleModel {
    protected $table = 'news_articles';
    protected $db;
    
    public function __construct() {
        require_once dirname(__DIR__) . '/config/database.php';
        $this->db = getDbConnection();
    }
    
    /**
     * 获取已发布文章列表
     */
    public function getPublishedArticles($categoryId = null, $page = 1, $pageSize = 20) {
        $offset = ($page - 1) * $pageSize;
        
        $sql = "SELECT n.*, u.username as author_name 
                FROM {$this->table} n
                LEFT JOIN users u ON n.article_author_id = u.id
                WHERE n.article_status = 'published' ";
        
        if ($categoryId) {
            $sql .= " AND n.article_category_id = ? ";
        }
        
        $sql .= " ORDER BY n.article_published_at DESC LIMIT ?, ?";
        
        $stmt = $this->db->prepare($sql);
        if ($categoryId) {
            $stmt->execute([$categoryId, $offset, $pageSize]);
        } else {
            $stmt->execute([$offset, $pageSize]);
        }
        
        return $stmt->fetchAll();
    }
    
    /**
     * 获取推荐文章
     */
    public function getFeaturedArticles($limit = 8) {
        $sql = "SELECT n.*, u.username as author_name 
                FROM {$this->table} n
                LEFT JOIN users u ON n.article_author_id = u.id
                WHERE n.article_status = 'published' AND n.article_is_featured = 1 
                ORDER BY n.article_published_at DESC 
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * 获取置顶文章
     */
    public function getTopArticles($limit = 5) {
        $sql = "SELECT n.*, u.username as author_name 
                FROM {$this->table} n
                LEFT JOIN users u ON n.article_author_id = u.id
                WHERE n.article_status = 'published' AND n.article_is_top = 1 
                ORDER BY n.article_published_at DESC 
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * 获取热门文章（按阅读量）
     */
    public function getHotArticles($limit = 10) {
        $sql = "SELECT n.*, u.username as author_name 
                FROM {$this->table} n
                LEFT JOIN users u ON n.article_author_id = u.id
                WHERE n.article_status = 'published' 
                ORDER BY n.article_read_count DESC, n.article_like_count DESC 
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * 根据别名获取文章（仅已发布）
     */
    public function getBySlug($slug) {
        $sql = "SELECT n.*, u.username as author_name 
                FROM {$this->table} n
                LEFT JOIN users u ON n.article_author_id = u.id
                WHERE n.article_slug = ? AND n.article_status = 'published'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
    
    /**
     * 根据别名获取文章（所有状态，用于后台预览）
     */
    public function getBySlugForPreview($slug) {
        $sql = "SELECT n.*, u.username as author_name 
                FROM {$this->table} n
                LEFT JOIN users u ON n.article_author_id = u.id
                WHERE n.article_slug = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
    
    /**
     * 根据栏目 ID 获取文章
     */
    public function getByCategory($categoryId) {
        $sql = "SELECT n.*, u.username as author_name 
                FROM {$this->table} n
                LEFT JOIN users u ON n.article_author_id = u.id
                WHERE n.article_category_id = ? AND n.article_status = 'published' 
                ORDER BY n.article_published_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }
    
    /**
     * 增加阅读量
     */
    public function incrementReadCount($id) {
        $sql = "UPDATE {$this->table} SET article_read_count = article_read_count + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * 更新文章
     */
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            $fields[] = "$field = ?";
            $params[] = $value;
        }
        
        $params[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * 删除文章
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * 根据 ID 获取文章
     */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * 搜索文章
     */
    public function searchArticles($keyword, $page = 1, $pageSize = PAGE_SIZE) {
        $offset = ($page - 1) * $pageSize;

        $sql = "SELECT n.*, u.username as author_name
                FROM {$this->table} n
                LEFT JOIN users u ON n.article_author_id = u.id
                WHERE n.article_status = 'published' AND
                      (n.article_title LIKE ? OR n.article_content LIKE ?)
                ORDER BY n.article_published_at DESC
                LIMIT ?, ?";

        $stmt = $this->db->prepare($sql);
        $searchTerm = "%{$keyword}%";
        $stmt->execute([$searchTerm, $searchTerm, $offset, $pageSize]);
        $data = $stmt->fetchAll();

        $countSql = "SELECT COUNT(*) as total FROM {$this->table}
                     WHERE article_status = 'published' AND
                           (article_title LIKE ? OR article_content LIKE ?)";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute([$searchTerm, $searchTerm]);
        $total = $countStmt->fetch()['total'];

        $totalPages = ceil($total / $pageSize);

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => $totalPages
        ];
    }
    
    /**
     * 获取相关文章
     */
    public function getRelatedArticles($articleId, $categoryId, $limit = 6) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE id != ? AND article_category_id = ? AND article_status = 'published' 
                ORDER BY article_published_at DESC 
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$articleId, $categoryId, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * 获取投稿列表
     */
    public function getContributions($userId = null, $status = null, $page = 1, $pageSize = PAGE_SIZE) {
        $conditions = ['article_source_type' => 'user_contribution'];
        
        if ($userId) {
            $conditions['article_author_id'] = $userId;
        }
        
        if ($status) {
            $conditions['article_status'] = $status;
        }
        
        return $this->paginate($conditions, 'article_published_at DESC', $page, $pageSize);
    }
    
    /**
     * 获取推送内容列表
     */
    public function getPushArticles($status = null, $page = 1, $pageSize = PAGE_SIZE) {
        $conditions = ['article_source_type' => 'subsite_push'];
        
        if ($status) {
            $conditions['article_status'] = $status;
        }
        
        return $this->paginate($conditions, 'article_published_at DESC', $page, $pageSize);
    }
    
    /**
     * 获取所有文章
     */
    public function getAll($conditions = [], $orderBy = 'id DESC', $limit = null) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                if (strpos($field, 'DATE(') !== false) {
                    $where[] = "$field = ?";
                } else {
                    $where[] = "$field = ?";
                }
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " ORDER BY $orderBy";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * 统计文章数量
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                if (strpos($field, 'DATE(') !== false) {
                    $where[] = "$field = ?";
                } else {
                    $where[] = "$field = ?";
                }
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
     * 分页获取数据
     */
    public function paginate($conditions = [], $orderBy = 'id DESC', $page = 1, $pageSize = PAGE_SIZE) {
        $offset = ($page - 1) * $pageSize;
        
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "$field = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " ORDER BY $orderBy LIMIT ?, ?";
        $params[] = $offset;
        $params[] = $pageSize;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        
        // 获取总数
        $countSql = "SELECT COUNT(*) as count FROM {$this->table}";
        $countParams = [];
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "$field = ?";
                $countParams[] = $value;
            }
            $countSql .= " WHERE " . implode(' AND ', $where);
        }
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($countParams);
        $total = $countStmt->fetch()['count'];
        
        $totalPages = ceil($total / $pageSize);
        
        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => $totalPages
        ];
    }
    
    /**
     * 获取文章总阅读量
     */
    public function getTotalReads($conditions = []) {
        $conditions['article_status'] = 'published';
        $sql = "SELECT COALESCE(SUM(article_read_count), 0) as total_reads FROM {$this->table}";
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
        return $result['total_reads'];
    }
    
    /**
     * 获取文章总点赞量
     */
    public function getTotalLikes($conditions = []) {
        $conditions['article_status'] = 'published';
        $sql = "SELECT COALESCE(SUM(article_like_count), 0) as total_likes FROM {$this->table}";
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
        return $result['total_likes'];
    }
    
    /**
     * 获取最近 N 天文章发布趋势
     */
    public function getPublishTrend($days = 7) {
        $sql = "
            SELECT DATE(article_published_at) as date, COUNT(*) as count 
            FROM {$this->table} 
            WHERE article_status = 'published' 
            AND article_published_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
            GROUP BY DATE(article_published_at)
            ORDER BY date ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * 获取热门文章（按创建时间）
     */
    public function getPopularArticles($limit = 10) {
        $sql = "SELECT id, article_title, article_slug, article_created_at 
                FROM {$this->table} 
                WHERE article_status = 'published' 
                ORDER BY article_created_at DESC 
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
?>