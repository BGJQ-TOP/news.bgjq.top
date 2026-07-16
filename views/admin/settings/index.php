<?php
$pageTitle = '系统设置';
?>

<!-- 页面标题 -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">
            <i class="fas fa-cog me-2 text-primary"></i>
            系统设置
        </h4>
        <nav aria-label="breadcrumb" class="mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">首页</a></li>
                <li class="breadcrumb-item active">系统设置</li>
            </ol>
        </nav>
    </div>
</div>

<!-- 提示信息 -->
<div class="alert alert-info mb-4">
    <i class="fas fa-info-circle me-2"></i>
    系统设置功能开发中。请联系管理员进行配置。
</div>

<!-- 设置选项卡 -->
<div class="admin-card">
    <div class="admin-card-header">
        <ul class="nav-tabs" style="margin-bottom: 0; border-bottom: none;">
            <li>
                <button class="nav-link active" id="basic-tab" onclick="showTab('basic')">
                    基本设置
                </button>
            </li>
            <li>
                <button class="nav-link" id="email-tab" onclick="showTab('email')">
                    邮件设置
                </button>
            </li>
            <li>
                <button class="nav-link" id="security-tab" onclick="showTab('security')">
                    安全设置
                </button>
            </li>
        </ul>
    </div>
    
    <div class="admin-card-body">
        <!-- 基本设置 -->
        <div id="basic-content" class="tab-content">
            <form>
                <div class="form-group">
                    <label class="form-label">网站名称</label>
                    <input type="text" class="form-control" value="<?php echo SITE_NAME; ?>" disabled>
                    <small class="text-muted">在网站标题处显示的名称</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">网站 URL</label>
                    <input type="text" class="form-control" value="<?php echo SITE_URL; ?>" disabled>
                    <small class="text-muted">网站的完整访问地址</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">每页显示数量</label>
                    <input type="text" class="form-control" value="<?php echo PAGE_SIZE; ?>" disabled>
                    <small class="text-muted">列表页每页显示的文章数量</small>
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    这些配置需要在 config/config.php 文件中修改
                </div>
            </form>
        </div>
        
        <!-- 邮件设置 -->
        <div id="email-content" class="tab-content" style="display: none;">
            <form>
                <div class="form-group">
                    <label class="form-label">SMTP 服务器</label>
                    <input type="text" class="form-control" placeholder="smtp.example.com" disabled>
                </div>
                
                <div class="form-group">
                    <label class="form-label">SMTP 端口</label>
                    <input type="number" class="form-control" placeholder="587" disabled>
                </div>
                
                <div class="form-group">
                    <label class="form-label">发件人邮箱</label>
                    <input type="email" class="form-control" placeholder="noreply@example.com" disabled>
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    邮件功能暂未启用
                </div>
            </form>
        </div>
        
        <!-- 安全设置 -->
        <div id="security-content" class="tab-content" style="display: none;">
            <form>
                <div class="form-group">
                    <label class="form-label">会话超时时间</label>
                    <input type="text" class="form-control" value="<?php echo SESSION_TIMEOUT; ?> 秒" disabled>
                    <small class="text-muted">管理员登录后无操作的超时时间</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">密码盐</label>
                    <input type="text" class="form-control" value="********" disabled>
                    <small class="text-muted">用于密码加密的盐值</small>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    安全设置已在配置文件中设定，不建议频繁修改
                </div>
            </form>
        </div>
    </div>
    
    <div class="admin-card-footer text-muted">
        <small>
            <i class="fas fa-lock me-1"></i>
            只有秘书长（超级管理员）可以访问系统设置
        </small>
    </div>
</div>

<script>
function showTab(tabName) {
    // 隐藏所有内容
    document.getElementById('basic-content').style.display = 'none';
    document.getElementById('email-content').style.display = 'none';
    document.getElementById('security-content').style.display = 'none';
    
    // 移除所有active类
    document.getElementById('basic-tab').classList.remove('active');
    document.getElementById('email-tab').classList.remove('active');
    document.getElementById('security-tab').classList.remove('active');
    
    // 显示选中的内容
    document.getElementById(tabName + '-content').style.display = 'block';
    document.getElementById(tabName + '-tab').classList.add('active');
}
</script>
