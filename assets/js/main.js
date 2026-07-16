// 邦国新闻 - 现代化 JavaScript 交互功能（优化版）
// 注意：header.php 和 footer.php 中已包含部分内联脚本，本文件通过命名空间和事件委托避免冲突

(function() {
    'use strict';

    // ==================== 工具函数 ====================

    /**
     * 防抖函数
     * @param {Function} func - 要执行的函数
     * @param {number} wait - 等待时间（毫秒）
     * @returns {Function}
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * 节流函数
     * @param {Function} func - 要执行的函数
     * @param {number} limit - 限制时间（毫秒）
     * @returns {Function}
     */
    function throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    /**
     * 检测是否偏好减少动画
     * @returns {boolean}
     */
    function prefersReducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    // ==================== 视图切换功能 ====================

    /**
     * 切换文章视图（网格/列表）
     * @param {string} viewType - 'grid' 或 'list'
     */
    function switchView(viewType) {
        const container = document.getElementById('articlesContainer');
        const gridBtn = document.getElementById('gridViewBtn');
        const listBtn = document.getElementById('listViewBtn');

        if (!container) return;

        // 移除所有视图类
        container.classList.remove('grid-view', 'list-view');

        // 更新按钮状态
        if (gridBtn) gridBtn.classList.remove('active');
        if (listBtn) listBtn.classList.remove('active');

        if (viewType === 'grid') {
            container.classList.add('grid-view');
            if (gridBtn) gridBtn.classList.add('active');
            localStorage.setItem('articleView', 'grid');
        } else {
            container.classList.add('list-view');
            if (listBtn) listBtn.classList.add('active');
            localStorage.setItem('articleView', 'list');
        }
    }

    /**
     * 初始化视图设置
     */
    function initViewSettings() {
        const savedView = localStorage.getItem('articleView') || 'grid';
        switchView(savedView);
    }

    // ==================== 搜索功能（带防抖） ====================

    /**
     * 初始化搜索功能
     */
    function enhanceSearch() {
        const searchInput = document.querySelector('.search-input');
        if (!searchInput) return;

        // 防抖搜索输入（可用于实时搜索建议）
        const debouncedSearch = debounce(function(value) {
            if (value.length > 0) {
                // 可在此添加搜索建议 AJAX 请求
                if (window.BGJQ && window.BGJQ.onSearchInput) {
                    window.BGJQ.onSearchInput(value);
                }
            }
        }, 300);

        searchInput.addEventListener('input', function(e) {
            debouncedSearch(e.target.value.trim());
        });

        // 搜索框获得焦点时添加类名
        searchInput.addEventListener('focus', function() {
            this.closest('.search-box')?.classList.add('is-focused');
        });

        searchInput.addEventListener('blur', function() {
            this.closest('.search-box')?.classList.remove('is-focused');
        });
    }

    // ==================== 图片懒加载（IntersectionObserver） ====================

    /**
     * 初始化图片懒加载
     */
    function initLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');
        if (!images.length) return;

        // 如果浏览器不支持 IntersectionObserver，直接加载所有图片
        if (!('IntersectionObserver' in window)) {
            images.forEach(img => {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
            });
            return;
        }

        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.dataset.src;

                    if (src) {
                        // 支持 reduced motion 时直接加载，否则添加淡入效果
                        if (prefersReducedMotion()) {
                            img.src = src;
                            img.removeAttribute('data-src');
                        } else {
                            img.style.opacity = '0';
                            img.style.transition = 'opacity 0.3s ease';
                            img.src = src;
                            img.onload = () => {
                                img.style.opacity = '1';
                                img.removeAttribute('data-src');
                            };
                            img.onerror = () => {
                                img.style.opacity = '1';
                                handleImageError(img);
                            };
                        }
                    }

                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.01
        });

        images.forEach(img => {
            imageObserver.observe(img);
        });
    }

    /**
     * 处理图片加载失败
     * @param {HTMLImageElement} img
     */
    function handleImageError(img) {
        img.style.display = 'none';
        const placeholder = img.closest('.article-card, .article-block')?.querySelector('.article-image-placeholder');
        if (placeholder) {
            placeholder.style.display = 'flex';
        }
    }

    // ==================== 主题切换（支持系统主题检测） ====================

    /**
     * 获取系统主题偏好
     * @returns {string} 'dark' 或 'light'
     */
    function getSystemTheme() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    /**
     * 应用主题
     * @param {string} mode - 'dark', 'light', 或 'system'
     */
    function applyTheme(mode) {
        const body = document.body;
        const themeIcon = document.getElementById('theme-icon');

        let actualMode = mode;
        if (mode === 'system') {
            actualMode = getSystemTheme();
        }

        body.setAttribute('data-theme', actualMode);

        // 更新图标
        if (themeIcon && window.BGJQ && window.BGJQ.icons) {
            const iconName = actualMode === 'dark' ? 'moon' : 'sun';
            const newIcon = window.BGJQ.icons.create(iconName, { size: 'sm', ariaHidden: true });
            newIcon.setAttribute('id', 'theme-icon');
            themeIcon.parentNode.replaceChild(newIcon, themeIcon);
        }

        // 更新 theme-color meta
        updateThemeColor(actualMode);
    }

    /**
     * 更新 theme-color meta 标签
     * @param {string} mode
     */
    function updateThemeColor(mode) {
        const metaThemeColor = document.querySelector('meta[name="theme-color"]:not([media])');
        if (metaThemeColor) {
            metaThemeColor.setAttribute('content', mode === 'dark' ? '#000000' : '#209cee');
        }
    }

    /**
     * 设置主题模式
     * @param {string} mode - 'dark', 'light', 或 'system'
     */
    function setThemeMode(mode) {
        if (mode === 'system') {
            localStorage.removeItem('theme');
        } else {
            localStorage.setItem('theme', mode);
        }
        applyTheme(mode);

        // 关闭下拉菜单
        const dropdown = document.getElementById('theme-dropdown-content');
        if (dropdown) {
            dropdown.classList.remove('is-open');
        }
        const toggleBtn = document.getElementById('theme-toggle');
        if (toggleBtn) {
            toggleBtn.setAttribute('aria-expanded', 'false');
        }
    }

    /**
     * 初始化主题
     */
    function initTheme() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            applyTheme(savedTheme);
        } else {
            applyTheme(getSystemTheme());
        }

        // 监听系统主题变化
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        const systemThemeHandler = (e) => {
            if (!localStorage.getItem('theme')) {
                applyTheme(e.matches ? 'dark' : 'light');
            }
        };

        // 使用现代 API（addEventListener）或降级方案（addListener）
        if (mediaQuery.addEventListener) {
            mediaQuery.addEventListener('change', systemThemeHandler);
        } else if (mediaQuery.addListener) {
            mediaQuery.addListener(systemThemeHandler);
        }
    }

    // ==================== 汉堡菜单切换 ====================

    /**
     * 切换汉堡菜单
     */
    function toggleHamburgerMenu() {
        const navMenu = document.getElementById('navMenu');
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        if (!navMenu || !hamburgerBtn) return;

        const isOpen = navMenu.classList.contains('is-open');

        if (isOpen) {
            navMenu.classList.remove('is-open');
            hamburgerBtn.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('nav-is-open');
        } else {
            navMenu.classList.add('is-open');
            hamburgerBtn.setAttribute('aria-expanded', 'true');
            document.body.classList.add('nav-is-open');
        }
    }

    /**
     * 初始化汉堡菜单
     */
    function initHamburgerMenu() {
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        if (!hamburgerBtn) return;

        // 避免重复绑定
        if (hamburgerBtn.dataset.bgjqBound === 'true') return;
        hamburgerBtn.dataset.bgjqBound = 'true';

        hamburgerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleHamburgerMenu();
        });
    }

    // ==================== 键盘导航支持 ====================

    /**
     * 初始化键盘导航
     */
    function initKeyboardNavigation() {
        document.addEventListener('keydown', function(e) {
            // ESC 键关闭下拉菜单和导航
            if (e.key === 'Escape') {
                // 关闭主题下拉
                const themeDropdown = document.getElementById('theme-dropdown-content');
                if (themeDropdown && themeDropdown.classList.contains('is-open')) {
                    themeDropdown.classList.remove('is-open');
                    const toggleBtn = document.getElementById('theme-toggle');
                    if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
                }

                // 关闭用户下拉
                const userDropdown = document.getElementById('user-dropdown-content');
                if (userDropdown && userDropdown.classList.contains('is-open')) {
                    userDropdown.classList.remove('is-open');
                    const btn = userDropdown.parentElement?.querySelector('button');
                    if (btn) btn.setAttribute('aria-expanded', 'false');
                }

                // 关闭汉堡菜单
                const navMenu = document.getElementById('navMenu');
                const hamburgerBtn = document.getElementById('hamburgerBtn');
                if (navMenu && navMenu.classList.contains('is-open')) {
                    navMenu.classList.remove('is-open');
                    if (hamburgerBtn) hamburgerBtn.setAttribute('aria-expanded', 'false');
                    document.body.classList.remove('nav-is-open');
                }

                // 关闭模态框
                const modals = document.querySelectorAll('.modal.open, .modal.is-open');
                modals.forEach(modal => {
                    modal.classList.remove('open', 'is-open');
                });
            }

            // Tab 键导航增强
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-navigation');
            }
        });

        // 鼠标点击时移除键盘导航类
        document.addEventListener('mousedown', function() {
            document.body.classList.remove('keyboard-navigation');
        });
    }

    // ==================== 下拉菜单外部点击关闭 ====================

    /**
     * 初始化点击外部关闭下拉菜单
     */
    function initDropdownClose() {
        document.addEventListener('click', function(e) {
            // 关闭主题下拉
            if (!e.target.closest('.theme-dropdown')) {
                const dropdown = document.getElementById('theme-dropdown-content');
                if (dropdown) dropdown.classList.remove('is-open');
                const toggleBtn = document.getElementById('theme-toggle');
                if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
            }

            // 关闭用户下拉
            if (!e.target.closest('.user-info-dropdown')) {
                const userDropdown = document.getElementById('user-dropdown-content');
                if (userDropdown) userDropdown.classList.remove('is-open');
                const btn = document.querySelector('.user-info-dropdown button');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }

            // 关闭汉堡菜单（点击导航外部）
            if (!e.target.closest('.main-nav') && !e.target.closest('.hamburger-btn')) {
                const navMenu = document.getElementById('navMenu');
                const hamburgerBtn = document.getElementById('hamburgerBtn');
                if (navMenu && navMenu.classList.contains('is-open')) {
                    navMenu.classList.remove('is-open');
                    if (hamburgerBtn) hamburgerBtn.setAttribute('aria-expanded', 'false');
                    document.body.classList.remove('nav-is-open');
                }
            }
        });
    }

    // ==================== 平滑滚动 ====================

    /**
     * 初始化平滑滚动
     */
    function initSmoothScroll() {
        if (prefersReducedMotion()) return;

        const links = document.querySelectorAll('a[href^="#"]');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;

                const targetId = href.substring(1);
                const targetElement = document.getElementById(targetId);

                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // ==================== 文章卡片交互 ====================

    /**
     * 初始化文章卡片交互
     */
    function enhanceArticleCards() {
        const cards = document.querySelectorAll('.article-card');

        cards.forEach(card => {
            // 鼠标悬停效果（仅在非 reduced motion 时）
            if (!prefersReducedMotion()) {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-4px)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            }

            // 点击卡片跳转（点击非链接区域时）
            card.addEventListener('click', function(e) {
                if (e.target.tagName !== 'A' && !e.target.closest('a')) {
                    const link = this.querySelector('.article-title a');
                    if (link) {
                        // 使用 Ctrl/Cmd 点击时在新标签页打开
                        if (e.ctrlKey || e.metaKey) {
                            window.open(link.href, '_blank');
                        } else {
                            window.location.href = link.href;
                        }
                    }
                }
            });
        });
    }

    // ==================== 加载更多功能 ====================

    /**
     * 初始化加载更多按钮
     */
    function initLoadMore() {
        const loadMoreBtn = document.querySelector('.load-more-btn');
        if (!loadMoreBtn) return;

        loadMoreBtn.addEventListener('click', async function() {
            this.classList.add('loading');
            this.disabled = true;

            try {
                // 模拟加载更多数据
                await new Promise(resolve => setTimeout(resolve, 1000));

                // 这里可以添加实际的 AJAX 请求
                if (window.BGJQ && window.BGJQ.onLoadMore) {
                    await window.BGJQ.onLoadMore();
                }
            } catch (error) {
                console.error('加载失败:', error);
            } finally {
                this.classList.remove('loading');
                this.disabled = false;
            }
        });
    }

    // ==================== 返回顶部按钮 ====================

    /**
     * 初始化返回顶部按钮
     */
    function initBackToTop() {
        const backToTop = document.getElementById('back-to-top');
        if (!backToTop) return;

        const toggleVisibility = throttle(function() {
            if (window.pageYOffset > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        }, 100);

        window.addEventListener('scroll', toggleVisibility);

        backToTop.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: prefersReducedMotion() ? 'auto' : 'smooth'
            });
        });
    }

    // ==================== 响应式图片处理 ====================

    /**
     * 处理响应式图片
     */
    function handleResponsiveImages() {
        const images = document.querySelectorAll('img');

        images.forEach(img => {
            // 添加加载失败处理
            img.addEventListener('error', function() {
                handleImageError(this);
            });

            // 添加加载成功处理
            img.addEventListener('load', function() {
                this.style.opacity = '1';
            });
        });
    }

    // ==================== 性能监控 ====================

    /**
     * 初始化性能监控
     */
    function initPerformanceMonitor() {
        if (!('performance' in window)) return;

        window.addEventListener('load', () => {
            const perfData = performance.getEntriesByType('navigation')[0];
            if (perfData) {
                console.log('邦国新闻 - 页面加载时间:', Math.round(perfData.loadEventEnd - perfData.navigationStart), 'ms');
            }
        });
    }

    // ==================== 初始化入口 ====================

    function init() {
        console.log('邦国新闻 - 现代化交互功能已加载');

        // 初始化所有功能
        initViewSettings();
        enhanceSearch();
        initLazyLoading();
        initSmoothScroll();
        enhanceArticleCards();
        initLoadMore();
        initKeyboardNavigation();
        initDropdownClose();
        initHamburgerMenu();
        initBackToTop();
        handleResponsiveImages();
        initPerformanceMonitor();

        // 初始化主题（如果 header/footer 中的脚本未执行）
        if (!document.body.getAttribute('data-theme')) {
            initTheme();
        }

        // 添加 CSS 类用于 JavaScript 检测
        document.body.classList.add('js-enabled');
    }

    // DOM 加载完成后初始化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // ==================== 全局错误处理 ====================

    window.addEventListener('error', function(e) {
        console.error('JavaScript 错误:', e.error);
    });

    // ==================== 导出 API ====================

    window.BGJQ = window.BGJQ || {};
    Object.assign(window.BGJQ, {
        switchView,
        initViewSettings,
        enhanceSearch,
        initLazyLoading,
        initSmoothScroll,
        enhanceArticleCards,
        initLoadMore,
        initKeyboardNavigation,
        initHamburgerMenu,
        toggleHamburgerMenu,
        debounce,
        throttle,
        prefersReducedMotion,
        handleResponsiveImages,
        setThemeMode,
        applyTheme,
        getSystemTheme,
        initTheme,
        updateThemeColor,
        initBackToTop
    });

})();
