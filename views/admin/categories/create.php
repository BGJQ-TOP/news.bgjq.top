<?php
$pageTitle = '创建栏目';
?>

<!-- 页面标题 -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">
            <i class="fas fa-folder-plus me-2 text-primary"></i>
            创建栏目
        </h4>
        <nav aria-label="breadcrumb" class="mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">首页</a></li>
                <li class="breadcrumb-item"><a href="/admin/categories">栏目管理</a></li>
                <li class="breadcrumb-item active">创建栏目</li>
            </ol>
        </nav>
    </div>
</div>

<!-- 栏目表单 -->
<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="mb-0">栏目信息</h5>
    </div>
    <div class="admin-card-body">
        <form id="categoryForm">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">栏目名称 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="category_name" required placeholder="请输入栏目名称">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">栏目编码 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="category_code" required placeholder="请输入栏目编码，如：news">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">栏目别名</label>
                        <input type="text" class="form-control" name="category_slug" placeholder="URL 别名，留空将自动生成">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">排序</label>
                        <input type="number" class="form-control" name="category_sort_order" value="0" min="0">
                        <small class="text-muted">数字越小排序越靠前</small>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">栏目描述</label>
                <textarea class="form-control" name="category_description" rows="3" placeholder="请输入栏目描述"></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">
                    <input type="checkbox" name="category_is_active" value="1" checked>
                    启用栏目
                </label>
            </div>
            
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="nes-btn is-success">
                    <i class="fas fa-save me-2"></i>创建栏目
                </button>
                <a href="/admin/categories" class="nes-btn">
                    <i class="fas fa-times me-2"></i>取消
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('categoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/categories/store', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('栏目创建成功', 'success');
            setTimeout(() => {
                window.location.href = '/admin/categories';
            }, 1000);
        } else {
            showToast(data.message || '创建失败', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('网络错误，请重试', 'error');
    });
});
</script>
