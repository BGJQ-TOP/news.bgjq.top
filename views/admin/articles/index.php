<?php
$pageTitle = '文章管理';
?>

<!-- 页面标题和操作按钮 -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">
            <i class="fas fa-newspaper me-2 text-primary"></i>
            文章管理
        </h4>
        <nav aria-label="breadcrumb" class="mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">首页</a></li>
                <li class="breadcrumb-item active">文章管理</li>
            </ol>
        </nav>
    </div>
    <div>
        <?php if ($this->adminUserModel->hasPermission($_SESSION['admin_role'], 'articles', 'create')): ?>
        <a href="/admin/articles/create" class="nes-btn is-success">
            <i class="fas fa-plus me-2"></i>发布新文章
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- 搜索和筛选表单 -->
<div class="admin-card mb-4">
    <div class="admin-card-body">
        <form method="get" class="row">
            <div class="col-md-4">
                <label for="keyword" class="form-label">关键词搜索</label>
                <input type="text" class="form-control" id="keyword" name="keyword" 
                       value="<?php echo htmlspecialchars($keyword); ?>" placeholder="搜索文章标题或内容...">
            </div>
            <div class="col-md-3">
                <label for="category_id" class="form-label">栏目筛选</label>
                <select class="form-select" id="category_id" name="category_id">
                    <option value="">全部栏目</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $currentCategory == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo $cat['category_name']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">状态筛选</label>
                <select class="form-select" id="status" name="status">
                    <option value="">全部状态</option>
                    <option value="draft" <?php echo $currentStatus == 'draft' ? 'selected' : ''; ?>>草稿</option>
                    <option value="pending" <?php echo $currentStatus == 'pending' ? 'selected' : ''; ?>>待审核</option>
                    <option value="published" <?php echo $currentStatus == 'published' ? 'selected' : ''; ?>>已发布</option>
                    <option value="rejected" <?php echo $currentStatus == 'rejected' ? 'selected' : ''; ?>>已驳回</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="nes-btn is-primary w-100">
                        <i class="fas fa-search me-2"></i>搜索
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 文章列表 -->
<div class="admin-card">
    <div class="admin-card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">文章列表</h5>
        <small class="text-muted">
            共 <?php echo $articles['total']; ?> 篇文章，
            第 <?php echo $articles['page']; ?> 页/共 <?php echo $articles['totalPages']; ?> 页
        </small>
    </div>
    
    <div class="admin-card-body p-0">
        <?php if (!empty($articles['data'])): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="40%">文章标题</th>
                        <th width="15%">栏目</th>
                        <th width="10%">状态</th>
                        <th width="15%">发布时间</th>
                        <th width="10%">阅读/点赞</th>
                        <th width="10%">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles['data'] as $article): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-start">
                                <?php if (!empty($article['article_cover_image'])): ?>
                                <img src="<?php echo $article['article_cover_image']; ?>" 
                                     alt="<?php echo $article['article_title']; ?>" 
                                     class="me-3" 
                                     style="width: 60px; height: 40px; object-fit: cover; border: 2px solid;">
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="/article/<?php echo $article['article_slug']; ?>" 
                                           target="_blank" 
                                           class="text-decoration-none" 
                                           title="<?php echo $article['article_title']; ?>">
                                            <?php echo truncate_string($article['article_title'], 40); ?>
                                        </a>
                                    </h6>
                                    <div class="small text-muted">
                                        <?php if (!empty($article['article_is_top'])): ?>
                                        <span class="badge bg-danger me-1">置顶</span>
                                        <?php endif; ?>
                                        <?php if (!empty($article['article_is_featured'])): ?>
                                        <span class="badge bg-warning me-1">推荐</span>
                                        <?php endif; ?>
                                        <?php if (!empty($article['article_is_headline'])): ?>
                                        <span class="badge bg-info">头条</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php 
                            $category = $this->categoryModel->getById($article['article_category_id']);
                            echo $category ? $category['category_name'] : '未知栏目';
                            ?>
                        </td>
                        <td>
                            <?php 
                            $statusConfig = [
                                'draft' => ['label' => '草稿', 'class' => 'secondary'],
                                'pending' => ['label' => '待审核', 'class' => 'warning'],
                                'published' => ['label' => '已发布', 'class' => 'success'],
                                'rejected' => ['label' => '已驳回', 'class' => 'danger']
                            ];
                            $status = $statusConfig[$article['article_status']] ?? ['label' => '未知', 'class' => 'secondary'];
                            ?>
                            <span class="badge bg-<?php echo $status['class']; ?>">
                                <?php echo $status['label']; ?>
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?php echo format_time($article['article_published_at'], 'm-d H:i'); ?>
                            </small>
                        </td>
                        <td>
                            <small class="text-muted">
                                <i class="fas fa-eye me-1"></i><?php echo $article['article_read_count']; ?><br>
                                <i class="fas fa-heart me-1"></i><?php echo $article['article_like_count']; ?>
                            </small>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="/admin/preview?slug=<?php echo $article['article_slug']; ?>" 
                                   target="_blank" 
                                   class="nes-btn is-primary" 
                                   title="预览">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <?php if ($this->adminUserModel->hasPermission($_SESSION['admin_role'], 'articles', 'edit')): ?>
                                <a href="/admin/articles/edit?id=<?php echo $article['id']; ?>" 
                                   class="nes-btn is-warning" 
                                   title="编辑">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if ($this->adminUserModel->hasPermission($_SESSION['admin_role'], 'articles', 'delete')): ?>
                                <button type="button" 
                                        class="nes-btn is-error" 
                                        title="删除"
                                        onclick="deleteArticle(<?php echo $article['id']; ?>, '<?php echo addslashes($article['article_title']); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- 分页 -->
        <?php if ($articles['totalPages'] > 1): ?>
        <div class="admin-card-footer">
            <nav aria-label="Page navigation">
                <div class="pagination">
                    <?php if ($articles['page'] > 1): ?>
                    <a class="nes-btn" href="?page=<?php echo $articles['page'] - 1; ?><?php echo $this->buildQueryString(['page']); ?>">
                        上一页
                    </a>
                    <?php endif; ?>
                    
                    <?php
                    $startPage = max(1, $articles['page'] - 2);
                    $endPage = min($articles['totalPages'], $articles['page'] + 2);
                    
                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                    <a href="?page=<?php echo $i; ?><?php echo $this->buildQueryString(['page']); ?>" class="nes-btn <?php echo $i == $articles['page'] ? 'is-success' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if ($articles['page'] < $articles['totalPages']): ?>
                    <a class="nes-btn" href="?page=<?php echo $articles['page'] + 1; ?><?php echo $this->buildQueryString(['page']); ?>">
                        下一页
                    </a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">暂无文章数据</h5>
            <p class="text-muted">尝试调整搜索条件或发布新文章</p>
            <?php if ($this->adminUserModel->hasPermission($_SESSION['admin_role'], 'articles', 'create')): ?>
            <a href="/admin/articles/create" class="nes-btn is-primary mt-3">
                <i class="fas fa-plus me-2"></i>发布第一篇文章
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// 删除文章确认
function deleteArticle(id, title) {
    const message = `确定要删除文章"${title}"吗？此操作不可撤销，且会删除所有相关数据。`;
    
    confirmAction(message, () => {
        const formData = new FormData();
        formData.append('id', id);
        
        fetch('/admin/articles/delete', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('文章删除成功', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast(data.message || '删除失败', 'danger');
            }
        })
        .catch(error => {
            console.error('删除失败:', error);
            showToast('网络错误，请重试', 'danger');
        });
    });
}
</script>
