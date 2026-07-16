<?php
$pageTitle = '发布新文章';
?>

<!-- 页面标题 -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">
            <i class="fas fa-plus me-2 text-primary"></i>
            发布新文章
        </h4>
        <nav aria-label="breadcrumb" class="mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">首页</a></li>
                <li class="breadcrumb-item"><a href="/admin/articles">文章管理</a></li>
                <li class="breadcrumb-item active">发布新文章</li>
            </ol>
        </nav>
    </div>
</div>

<!-- 文章表单 -->
<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="mb-0">文章信息</h5>
    </div>
    <div class="admin-card-body">
        <form id="articleForm">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="form-label">文章标题 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="article_title" required placeholder="请输入文章标题">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">所属栏目 <span class="text-danger">*</span></label>
                        <select class="form-select" name="article_category_id" required>
                            <option value="">请选择栏目</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">封面图片 URL</label>
                <input type="url" class="form-control" name="article_cover_image" placeholder="https://example.com/image.jpg">
                <small class="text-muted">留空将使用默认封面</small>
            </div>
            
            <div class="form-group">
                <label class="form-label">文章摘要</label>
                <textarea class="form-control" name="article_summary" rows="3" placeholder="请输入文章摘要，留空将自动从内容中提取"></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">文章内容 <span class="text-danger">*</span></label>
                <textarea class="form-control" name="article_content" rows="15" required placeholder="请输入文章内容，支持 HTML 格式"></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" name="is_featured" value="1">
                            推荐文章
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" name="is_top" value="1">
                            置顶文章
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" name="is_headline" value="1">
                            头条文章
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="nes-btn is-success">
                    <i class="fas fa-save me-2"></i>发布文章
                </button>
                <a href="/admin/articles" class="nes-btn">
                    <i class="fas fa-times me-2"></i>取消
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('articleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/articles/store', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('文章发布成功', 'success');
            setTimeout(() => {
                window.location.href = '/admin/articles';
            }, 1000);
        } else {
            showToast(data.message || '发布失败', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('网络错误，请重试', 'error');
    });
});
</script>
