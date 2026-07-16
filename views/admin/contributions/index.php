<?php
$pageTitle = '投稿审核';
?>

<!-- 页面标题和状态切换 -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">
            <i class="fas fa-edit me-2 text-primary"></i>
            投稿审核
        </h4>
        <nav aria-label="breadcrumb" class="mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">首页</a></li>
                <li class="breadcrumb-item active">投稿审核</li>
            </ol>
        </nav>
    </div>
</div>

<!-- 状态切换标签 -->
<ul class="nav-tabs mb-4">
    <li>
        <a class="nav-link <?php echo $currentStatus === 'pending' ? 'active' : ''; ?>" 
           href="/admin/contributions?status=pending">
            待审核
        </a>
    </li>
    <li>
        <a class="nav-link <?php echo $currentStatus === 'published' ? 'active' : ''; ?>" 
           href="/admin/contributions?status=published">
            已通过
        </a>
    </li>
    <li>
        <a class="nav-link <?php echo $currentStatus === 'rejected' ? 'active' : ''; ?>" 
           href="/admin/contributions?status=rejected">
            已拒绝
        </a>
    </li>
</ul>

<!-- 投稿列表 -->
<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="mb-0">
            <?php if ($currentStatus === 'pending'): ?>
                待审核投稿
            <?php elseif ($currentStatus === 'published'): ?>
                已通过投稿
            <?php else: ?>
                已拒绝投稿
            <?php endif; ?>
        </h5>
    </div>
    
    <div class="admin-card-body p-0">
        <?php if (!empty($contributions['data'])): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="30%">文章标题</th>
                        <th width="15%">作者</th>
                        <th width="10%">栏目</th>
                        <th width="15%">投稿时间</th>
                        <th width="10%">状态</th>
                        <th width="15%">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contributions['data'] as $contribution): ?>
                    <tr>
                        <td><?php echo $contribution['id']; ?></td>
                        <td>
                            <?php if ($contribution['article_status'] === 'published'): ?>
                                <a href="/article/<?php echo $contribution['article_slug']; ?>" target="_blank">
                                    <?php echo htmlspecialchars($contribution['article_title']); ?>
                                </a>
                            <?php else: ?>
                                <a href="/admin/preview?slug=<?php echo $contribution['article_slug']; ?>" target="_blank">
                                    <?php echo htmlspecialchars($contribution['article_title']); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (isset($contribution['author_name'])): ?>
                                <i class="fas fa-user me-1 text-muted"></i>
                                <?php echo htmlspecialchars($contribution['author_name']); ?>
                            <?php else: ?>
                                <span class="text-muted">未知</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-info"><?php echo $contribution['article_category_id']; ?></span>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($contribution['article_created_at'])); ?></td>
                        <td>
                            <?php if ($contribution['article_status'] === 'pending'): ?>
                                <span class="badge bg-warning">待审核</span>
                            <?php elseif ($contribution['article_status'] === 'published'): ?>
                                <span class="badge bg-success">已发布</span>
                            <?php elseif ($contribution['article_status'] === 'rejected'): ?>
                                <span class="badge bg-danger">已拒绝</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($contribution['article_status'] === 'pending'): ?>
                                <button type="button" class="nes-btn is-success btn-approve" data-id="<?php echo $contribution['id']; ?>">
                                    <i class="fas fa-check"></i> 通过
                                </button>
                                <button type="button" class="nes-btn is-error btn-reject" data-id="<?php echo $contribution['id']; ?>">
                                    <i class="fas fa-times"></i> 拒绝
                                </button>
                            <?php elseif ($contribution['article_status'] === 'rejected'): ?>
                                <button type="button" class="nes-btn btn-reapprove" data-id="<?php echo $contribution['id']; ?>">
                                    <i class="fas fa-redo"></i> 重新审核
                                </button>
                            <?php endif; ?>
                            
                            <button type="button" class="nes-btn is-primary btn-view" data-id="<?php echo $contribution['id']; ?>">
                                <i class="fas fa-eye"></i> 查看
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- 分页 -->
        <?php if ($contributions['totalPages'] > 1): ?>
        <div class="admin-card-footer">
            <nav aria-label="Page navigation">
                <div class="pagination">
                    <?php if ($contributions['page'] > 1): ?>
                    <a class="nes-btn" href="?status=<?php echo $currentStatus; ?>&page=<?php echo $contributions['page'] - 1; ?>">上一页</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $contributions['totalPages']; $i++): ?>
                    <a class="nes-btn <?php echo $i == $contributions['page'] ? 'is-success' : ''; ?>" href="?status=<?php echo $currentStatus; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    
                    <?php if ($contributions['page'] < $contributions['totalPages']): ?>
                    <a class="nes-btn" href="?status=<?php echo $currentStatus; ?>&page=<?php echo $contributions['page'] + 1; ?>">下一页</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-clipboard-check text-muted" style="font-size: 4rem;"></i>
            <p class="text-muted mt-3">
                <?php if ($currentStatus === 'pending'): ?>
                    暂无待审核的投稿
                <?php elseif ($currentStatus === 'published'): ?>
                    暂无已通过的投稿
                <?php else: ?>
                    暂无被拒绝的投稿
                <?php endif; ?>
            </p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- 拒绝原因模态框 -->
<div class="nes-container with-title" id="rejectModal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999; max-width: 500px; width: 90%;">
    <p class="title">拒绝原因</p>
    <div style="margin-bottom: 16px;">
        <textarea class="form-control" id="rejectReason" rows="4" placeholder="请输入拒绝原因..."></textarea>
    </div>
    <div style="display: flex; gap: 8px; justify-content: flex-end;">
        <button type="button" class="nes-btn" onclick="closeRejectModal()">取消</button>
        <button type="button" class="nes-btn is-error" id="confirmReject">拒绝</button>
    </div>
</div>

<script>
let currentArticleId = null;

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    // 通过审核
    document.querySelectorAll('.btn-approve').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            if (confirm('确定要通过这篇投稿吗？')) {
                fetch('/admin/contributions/approve', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('审核通过');
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
    
    // 拒绝审核
    document.querySelectorAll('.btn-reject').forEach(function(btn) {
        btn.addEventListener('click', function() {
            currentArticleId = this.dataset.id;
            document.getElementById('rejectReason').value = '';
            document.getElementById('rejectModal').style.display = 'block';
        });
    });
    
    document.getElementById('confirmReject').addEventListener('click', function() {
        const reason = document.getElementById('rejectReason').value;
        if (!reason) {
            alert('请输入拒绝原因');
            return;
        }
        
        fetch('/admin/contributions/reject', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + currentArticleId + '&reason=' + encodeURIComponent(reason)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('已拒绝');
                location.reload();
            } else {
                alert('操作失败：' + data.message);
            }
        })
        .catch(error => {
            alert('请求失败：' + error);
        })
        .finally(() => {
            closeRejectModal();
        });
    });
    
    // 重新审核
    document.querySelectorAll('.btn-reapprove').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            if (confirm('确定要重新审核这篇投稿吗？')) {
                alert('功能开发中...');
            }
        });
    });
    
    // 查看详情
    document.querySelectorAll('.btn-view').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const row = this.closest('tr');
            const link = row.querySelector('td:nth-child(2) a');
            if (link) {
                window.open(link.href, '_blank');
            } else {
                window.open('/admin/articles/edit?id=' + id, '_blank');
            }
        });
    });
});
</script>
