        </main>
    </div>

    <!-- 脚本文件 -->
    <script src="<?php echo ASSET_URL; ?>/js/main.js"></script>

    <!-- 统计代码（仅超级管理员可见） -->
    <?php if ($_SESSION['admin_role'] === 'super_admin'): ?>
    <script>
        console.log('后台管理系统 - <?php echo SITE_NAME; ?>');
        console.log('当前用户: <?php echo $_SESSION['admin_username']; ?>');
        console.log('用户角色: <?php echo $_SESSION['admin_role']; ?>');
        console.log('登录时间: <?php echo date('Y-m-d H:i:s', $_SESSION['admin_login_time']); ?>');
    </script>
    <?php endif; ?>

    <!-- 全局函数 -->
    <script>
    // 确认对话框
    function confirmAction(message, callback) {
        if (confirm(message)) {
            callback();
        }
    }

    // 显示消息提示
    function showToast(message, type = 'success') {
        // 创建NES风格的提示
        const toast = document.createElement('div');
        toast.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 300px;';

        const containerClass = type === 'success' ? 'is-success' : type === 'error' ? 'is-error' : 'is-primary';

        toast.innerHTML = `
            <div class="nes-container ${containerClass} with-title" style="margin-bottom: 0;">
                <p class="title" style="font-size: 14px;">提示</p>
                <p style="font-size: 16px;">${message}</p>
            </div>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // AJAX请求封装
    function ajaxRequest(url, data = {}, method = 'POST') {
        return fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: method === 'POST' ? JSON.stringify(data) : null
        })
        .then(response => response.json())
        .catch(error => {
            console.error('AJAX请求失败:', error);
            throw error;
        });
    }

    // 表单验证
    function validateForm(form) {
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.style.borderColor = '#e76e55';
                isValid = false;
            } else {
                input.style.borderColor = '';
            }
        });

        return isValid;
    }

    // 移动端侧边栏控制
    function initMobileSidebar() {
        const menuToggle = document.getElementById('menuToggle');
        const sidebarClose = document.getElementById('sidebarClose');
        const adminSidebar = document.getElementById('adminSidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if (!menuToggle || !adminSidebar) return;

        function openSidebar() {
            adminSidebar.classList.add('active');
            if (sidebarOverlay) sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            adminSidebar.classList.remove('active');
            if (sidebarOverlay) sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        menuToggle.addEventListener('click', openSidebar);
        if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

        // 点击侧边栏内的导航链接后自动关闭
        const navLinks = adminSidebar.querySelectorAll('.nav-link');
        navLinks.forEach(function(link) {
            link.addEventListener('click', closeSidebar);
        });

        // ESC 键关闭侧边栏
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && adminSidebar.classList.contains('active')) {
                closeSidebar();
            }
        });
    }

    // 页面加载完成后执行
    document.addEventListener('DOMContentLoaded', function() {
        // 初始化移动端侧边栏
        initMobileSidebar();

        // 表单提交处理
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!validateForm(this)) {
                    e.preventDefault();
                    showToast('请检查表单中的必填项', 'error');
                }
            });
        });

        // 确认删除操作
        const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const message = this.getAttribute('data-confirm-delete') || '确定要删除吗？此操作不可撤销。';
                const url = this.href;

                if (confirm(message)) {
                    fetch(url, { method: 'DELETE' })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showToast('删除成功', 'success');
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                showToast(data.message || '删除失败', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('删除失败:', error);
                            showToast('网络错误，请重试', 'error');
                        });
                }
            });
        });
    });
    </script>
</body>
</html>
