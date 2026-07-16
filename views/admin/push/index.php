<?php
$pageTitle = '推送管理';
?>

<!-- 页面标题 -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">
            <i class="fas fa-share-square me-2 text-primary"></i>
            推送管理
        </h4>
        <nav aria-label="breadcrumb" class="mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">首页</a></li>
                <li class="breadcrumb-item active">推送管理</li>
            </ol>
        </nav>
    </div>
</div>

<!-- 提示信息 -->
<div class="alert alert-info mb-4">
    <i class="fas fa-info-circle me-2"></i>
    推送内容会显示在子站首页的推荐位置。只有秘书长可以管理推送内容。
</div>

<!-- 推送文章列表 -->
<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="mb-0">推送文章列表</h5>
    </div>
    
    <div class="admin-card-body p-0">
        <?php if (!empty($pushArticles['data'])): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="35%">文章标题</th>
                        <th width="15%">栏目</th>
                        <th width="15%">发布时间</th>
                        <th width="15%">推送时间</th>
                        <th width="15%">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pushArticles['data'] as $article): ?>
                    <tr>
                        <td><?php echo $article['id']; ?></td>
                        <td>
                            <a href="/article/<?php echo $article['article_slug']; ?>" target="_blank">
                                <?php echo htmlspecialchars($article['article_title']); ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-info"><?php echo $article['article_category_id']; ?></span>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($article['article_published_at'])); ?></td>
                        <td>-</td>
                        <td>
                            <button type="button" class="nes-btn is-error btn-remove" data-id="<?php echo $article['id']; ?>">
                                <i class="fas fa-times"></i> 移除推送
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- 分页 -->
        <?php if ($pushArticles['totalPages'] > 1): ?>
        <div class="admin-card-footer">
            <nav aria-label="Page navigation">
                <div class="pagination">
                    <?php if ($pushArticles['page'] > 1): ?>
                    <a class="nes-btn" href="?page=<?php echo $pushArticles['page'] - 1; ?>">上一页</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $pushArticles['totalPages']; $i++): ?>
                    <a class="nes-btn <?php echo $i == $pushArticles['page'] ? 'is-success' : ''; ?>" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    
                    <?php if ($pushArticles['page'] < $pushArticles['totalPages']): ?>
                    <a class="nes-btn" href="?page=<?php echo $pushArticles['page'] + 1; ?>">下一页</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-share-square text-muted" style="font-size: 4rem;"></i>
            <p class="text-muted mt-3">暂无推送文章</p>
            <p class="text-muted">可以从文章管理页面添加文章到推送</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 移除推送
    document.querySelectorAll('.btn-remove').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            if (confirm('确定要移除这篇推送文章吗？')) {
                fetch('/admin/push/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'article_id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('移除成功');
                        location.reload();
                    } else {
                        alert('操作失败：' + data.message);
                    }
                })
                .catch(error => {
                    alert('请求失败：' + error);
                });
            }
        });
    });
});
</script>
