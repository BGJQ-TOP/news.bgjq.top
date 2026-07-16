    </main>

    <!-- 页脚 -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h5><i data-icon="star" data-size="sm" aria-hidden="true"></i> <?php echo SITE_NAME; ?></h5>
                    <p class="footer-description">
                        <?php echo SITE_DESCRIPTION; ?>
                    </p>
                    <div class="mt-2 footer-meta">
                        <p><i data-icon="server" data-size="sm" aria-hidden="true"></i> 服务器IP: bgjq.simpfun.cn</p>
                        <p><i data-icon="heart" data-size="sm" aria-hidden="true"></i> 官方QQ群: 1081785684</p>
                    </div>
                </div>

                <div class="footer-section">
                    <h5><i data-icon="list" data-size="sm" aria-hidden="true"></i> 快速链接</h5>
                    <ul>
                        <li><a href="/">首页</a></li>
                        <?php
                        $categories = (new CategoryModel())->getActiveCategories();
                        foreach ($categories as $category):
                        ?>
                        <li><a href="/category/<?php echo isset($category['category_slug']) ? $category['category_slug'] : ''; ?>/">
                            <?php echo isset($category['category_name']) ? $category['category_name'] : ''; ?>
                        </a></li>
                        <?php endforeach; ?>
                        <li><a href="/rankings">排行榜</a></li>
                        <li><a href="/contribute">我要投稿</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h5><i data-icon="link" data-size="sm" aria-hidden="true"></i> 生态链接</h5>
                    <ul>
                        <li><a href="https://bgjq.top" target="_blank" rel="noopener">主站</a></li>
                        <li><a href="https://8w.bgjq.top" target="_blank" rel="noopener">8w社区</a></li>
                        <li><a href="https://wiki.bgjq.top" target="_blank" rel="noopener">Wiki百科</a></li>
                        <li><a href="https://countries.bgjq.top" target="_blank" rel="noopener">邦国主页</a></li>
                        <li><a href="https://bgjq.lyizai.top" target="_blank" rel="noopener">养老院邦国</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. 保留所有权利.</p>
                <p class="mt-1">
                    <a href="/sitemap.xml" class="nes-text is-primary">网站地图</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- 返回顶部按钮 -->
    <button id="back-to-top" class="nes-btn is-primary back-to-top" title="返回顶部" aria-label="返回顶部">
        <i data-icon="chevron-up" data-size="sm" aria-hidden="true"></i>
    </button>

    <!-- 脚本文件 -->
    <script src="<?php echo ASSET_URL; ?>/js/main.js"></script>

    <!-- 主题切换脚本 -->
    <script>
        // 与 header.php 保持一致的主题切换逻辑
        function getSystemTheme() {
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        function applyTheme(mode) {
            const body = document.body;
            if (mode === 'system') {
                mode = getSystemTheme();
            }
            body.setAttribute('data-theme', mode);
            updateThemeIcon(mode);
            updateThemeColor(mode);
        }

        function updateThemeIcon(mode) {
            const themeIcon = document.getElementById('theme-icon');
            if (!themeIcon || !window.BGJQ || !window.BGJQ.icons) return;

            const iconName = mode === 'dark' ? 'moon' : 'sun';
            const newIcon = window.BGJQ.icons.create(iconName, { size: 'sm', ariaHidden: true });
            newIcon.setAttribute('id', 'theme-icon');
            themeIcon.parentNode.replaceChild(newIcon, themeIcon);
        }

        function updateThemeColor(mode) {
            const metaThemeColor = document.querySelector('meta[name="theme-color"]:not([media])');
            if (metaThemeColor) {
                metaThemeColor.setAttribute('content', mode === 'dark' ? '#000000' : '#209cee');
            }
        }

        function setThemeMode(mode) {
            if (mode === 'system') {
                localStorage.removeItem('theme');
            } else {
                localStorage.setItem('theme', mode);
            }
            applyTheme(mode);
        }

        function initTheme() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                applyTheme(savedTheme);
            } else {
                applyTheme(getSystemTheme());
            }

            // 监听系统主题变化
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
                if (!localStorage.getItem('theme')) {
                    applyTheme(getSystemTheme());
                }
            });
        }

        // 返回顶部按钮
        const backToTop = document.getElementById('back-to-top');

        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });

        backToTop.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // 页面加载时初始化主题
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTheme);
        } else {
            initTheme();
        }
    </script>

    <!-- 统计代码 -->
    <script>
        console.log('<?php echo SITE_NAME; ?> - 页面加载完成');
    </script>
</body>
</html>
