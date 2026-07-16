/**
 * 邦国新闻 - SVG 图标系统
 * 自动将 <i data-icon="name"></i> 替换为对应的 SVG 图标
 *
 * 使用方法：
 *   <i data-icon="fire"></i>
 *   <i data-icon="heart" data-size="lg"></i>
 *   <i data-icon="search" aria-hidden="true"></i>
 */
(function () {
    'use strict';

    const SIZE_MAP = {
        sm: 16,
        md: 24,
        lg: 32,
        xl: 48
    };

    // SVG 图标定义
    const ICONS = {
        sun: '<rect x="9" y="9" width="6" height="6" fill="none" stroke="currentColor" stroke-width="2"/><line x1="12" y1="2" x2="12" y2="5" stroke="currentColor" stroke-width="2"/><line x1="12" y1="19" x2="12" y2="22" stroke="currentColor" stroke-width="2"/><line x1="2" y1="12" x2="5" y2="12" stroke="currentColor" stroke-width="2"/><line x1="19" y1="12" x2="22" y2="12" stroke="currentColor" stroke-width="2"/><line x1="4.5" y1="4.5" x2="6.5" y2="6.5" stroke="currentColor" stroke-width="2"/><line x1="17.5" y1="17.5" x2="19.5" y2="19.5" stroke="currentColor" stroke-width="2"/><line x1="4.5" y1="19.5" x2="6.5" y2="17.5" stroke="currentColor" stroke-width="2"/><line x1="17.5" y1="6.5" x2="19.5" y2="4.5" stroke="currentColor" stroke-width="2"/>',
        moon: '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="miter" d="M18 4 L16 6 L16 10 L18 12 L20 10 L20 6 Z M14 8 L12 10 L12 18 L14 20 L18 20 L20 18 L20 14"/>',
        monitor: '<rect x="2" y="4" width="20" height="14" fill="none" stroke="currentColor" stroke-width="2"/><line x1="8" y1="18" x2="16" y2="18" stroke="currentColor" stroke-width="2"/><line x1="10" y1="18" x2="10" y2="22" stroke="currentColor" stroke-width="2"/><line x1="14" y1="18" x2="14" y2="22" stroke="currentColor" stroke-width="2"/><line x1="6" y1="22" x2="18" y2="22" stroke="currentColor" stroke-width="2"/>',
        user: '<rect x="8" y="4" width="8" height="8" fill="none" stroke="currentColor" stroke-width="2"/><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="miter" d="M4 20 L6 14 L18 14 L20 20 Z"/>',
        search: '<circle cx="11" cy="11" r="7" fill="none" stroke="currentColor" stroke-width="2"/><line x1="16" y1="16" x2="22" y2="22" stroke="currentColor" stroke-width="2"/>',
        fire: '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="miter" d="M12 2 L14 6 L12 8 L10 6 Z M10 6 L8 10 L10 14 L12 12 L14 14 L16 10 L14 6 M8 10 L6 14 L8 18 L10 16 L12 18 L14 16 L16 18 L18 14 L16 10 M8 18 L8 22 L16 22 L16 18"/>',
        heart: '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="miter" d="M12 21 L4 13 L4 7 L6 5 L10 5 L12 7 L14 5 L18 5 L20 7 L20 13 Z"/>',
        clock: '<rect x="2" y="2" width="20" height="20" rx="0" fill="none" stroke="currentColor" stroke-width="2"/><polyline points="12,6 12,12 16,12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="miter"/>',
        eye: '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="miter" d="M2 12 L6 8 L10 6 L14 6 L18 8 L22 12 L18 16 L14 18 L10 18 L6 16 Z"/><rect x="10" y="10" width="4" height="4" fill="none" stroke="currentColor" stroke-width="2"/>',
        calendar: '<rect x="2" y="4" width="20" height="18" fill="none" stroke="currentColor" stroke-width="2"/><line x1="2" y1="10" x2="22" y2="10" stroke="currentColor" stroke-width="2"/><line x1="6" y1="2" x2="6" y2="6" stroke="currentColor" stroke-width="2"/><line x1="18" y1="2" x2="18" y2="6" stroke="currentColor" stroke-width="2"/><rect x="6" y="14" width="4" height="2" fill="currentColor"/><rect x="14" y="14" width="4" height="2" fill="currentColor"/>',
        newspaper: '<rect x="2" y="2" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"/><line x1="6" y1="6" x2="18" y2="6" stroke="currentColor" stroke-width="2"/><line x1="6" y1="10" x2="14" y2="10" stroke="currentColor" stroke-width="2"/><line x1="6" y1="14" x2="18" y2="14" stroke="currentColor" stroke-width="2"/><line x1="6" y1="18" x2="12" y2="18" stroke="currentColor" stroke-width="2"/><rect x="16" y="10" width="4" height="8" fill="none" stroke="currentColor" stroke-width="2"/>',
        trophy: '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="miter" d="M6 4 L6 10 L8 14 L12 16 L16 14 L18 10 L18 4 Z"/><line x1="4" y1="6" x2="6" y2="6" stroke="currentColor" stroke-width="2"/><line x1="18" y1="6" x2="20" y2="6" stroke="currentColor" stroke-width="2"/><line x1="4" y1="6" x2="4" y2="8" stroke="currentColor" stroke-width="2"/><line x1="20" y1="6" x2="20" y2="8" stroke="currentColor" stroke-width="2"/><line x1="10" y1="16" x2="10" y2="20" stroke="currentColor" stroke-width="2"/><line x1="14" y1="16" x2="14" y2="20" stroke="currentColor" stroke-width="2"/><line x1="8" y1="20" x2="16" y2="20" stroke="currentColor" stroke-width="2"/>',
        link: '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="miter" d="M10 6 L6 6 L6 18 L10 18"/><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="miter" d="M14 6 L18 6 L18 18 L14 18"/><line x1="8" y1="12" x2="16" y2="12" stroke="currentColor" stroke-width="2"/>',
        tag: '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="miter" d="M2 12 L10 4 L20 4 L20 14 L12 22 Z"/><rect x="14" y="8" width="4" height="4" fill="none" stroke="currentColor" stroke-width="2"/>',
        star: '<path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="miter" d="M12 2 L14 8 L20 8 L16 12 L18 18 L12 14 L6 18 L8 12 L4 8 L10 8 Z"/>',
        chat: '<rect x="2" y="4" width="20" height="14" fill="none" stroke="currentColor" stroke-width="2"/><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="miter" d="M6 18 L10 18 L12 22 L14 18 L18 18"/><line x1="6" y1="9" x2="14" y2="9" stroke="currentColor" stroke-width="2"/><line x1="6" y1="13" x2="18" y2="13" stroke="currentColor" stroke-width="2"/>',
        book: '<rect x="4" y="2" width="16" height="20" fill="none" stroke="currentColor" stroke-width="2"/><line x1="8" y1="2" x2="8" y2="22" stroke="currentColor" stroke-width="2"/><line x1="12" y1="6" x2="16" y2="6" stroke="currentColor" stroke-width="2"/><line x1="12" y1="10" x2="16" y2="10" stroke="currentColor" stroke-width="2"/><line x1="12" y1="14" x2="16" y2="14" stroke="currentColor" stroke-width="2"/><line x1="12" y1="18" x2="16" y2="18" stroke="currentColor" stroke-width="2"/>',
        close: '<line x1="4" y1="4" x2="20" y2="20" stroke="currentColor" stroke-width="2"/><line x1="20" y1="4" x2="4" y2="20" stroke="currentColor" stroke-width="2"/>',
        setting: '<circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2"/><path fill="none" stroke="currentColor" stroke-width="2" d="M12 2 L14 4 L14 6 L16 6 L18 4 L20 6 L18 8 L18 10 L20 12 L18 14 L18 16 L20 18 L18 20 L16 18 L14 18 L14 20 L12 22 L10 20 L10 18 L8 18 L6 20 L4 18 L6 16 L6 14 L4 12 L6 10 L6 8 L4 6 L6 4 L8 6 L10 6 L10 4 Z"/>',
        menu: '<line x1="4" y1="6" x2="20" y2="6" stroke="currentColor" stroke-width="2"/><line x1="4" y1="12" x2="20" y2="12" stroke="currentColor" stroke-width="2"/><line x1="4" y1="18" x2="20" y2="18" stroke="currentColor" stroke-width="2"/>',
        grid: '<rect x="4" y="4" width="6" height="6" fill="none" stroke="currentColor" stroke-width="2"/><rect x="14" y="4" width="6" height="6" fill="none" stroke="currentColor" stroke-width="2"/><rect x="4" y="14" width="6" height="6" fill="none" stroke="currentColor" stroke-width="2"/><rect x="14" y="14" width="6" height="6" fill="none" stroke="currentColor" stroke-width="2"/>',
        list: '<line x1="4" y1="6" x2="20" y2="6" stroke="currentColor" stroke-width="2"/><line x1="4" y1="12" x2="20" y2="12" stroke="currentColor" stroke-width="2"/><line x1="4" y1="18" x2="20" y2="18" stroke="currentColor" stroke-width="2"/><rect x="4" y="4" width="4" height="4" fill="currentColor"/><rect x="4" y="10" width="4" height="4" fill="currentColor"/><rect x="4" y="16" width="4" height="4" fill="currentColor"/>',
        arrowLeft: '<line x1="20" y1="12" x2="4" y2="12" stroke="currentColor" stroke-width="2"/><polyline points="10,6 4,12 10,18" fill="none" stroke="currentColor" stroke-width="2"/>',
        arrowRight: '<line x1="4" y1="12" x2="20" y2="12" stroke="currentColor" stroke-width="2"/><polyline points="14,6 20,12 14,18" fill="none" stroke="currentColor" stroke-width="2"/>',
        arrowUp: '<line x1="12" y1="20" x2="12" y2="4" stroke="currentColor" stroke-width="2"/><polyline points="6,10 12,4 18,10" fill="none" stroke="currentColor" stroke-width="2"/>',
        arrowDown: '<line x1="12" y1="4" x2="12" y2="20" stroke="currentColor" stroke-width="2"/><polyline points="6,14 12,20 18,14" fill="none" stroke="currentColor" stroke-width="2"/>',
        check: '<polyline points="4,12 9,17 20,6" fill="none" stroke="currentColor" stroke-width="2"/>',
        edit: '<path fill="none" stroke="currentColor" stroke-width="2" d="M4 16 L4 20 L8 20 L18 10 L14 6 L4 16 Z"/><line x1="14" y1="6" x2="18" y2="10" stroke="currentColor" stroke-width="2"/>',
        trash: '<rect x="4" y="6" width="16" height="2" fill="currentColor"/><rect x="6" y="8" width="12" height="14" fill="none" stroke="currentColor" stroke-width="2"/><line x1="10" y1="4" x2="14" y2="4" stroke="currentColor" stroke-width="2"/>',
        plus: '<line x1="12" y1="4" x2="12" y2="20" stroke="currentColor" stroke-width="2"/><line x1="4" y1="12" x2="20" y2="12" stroke="currentColor" stroke-width="2"/>',
        bell: '<path fill="none" stroke="currentColor" stroke-width="2" d="M4 8 L4 12 L6 16 L18 16 L20 12 L20 8 L18 4 L6 4 Z"/><line x1="10" y1="20" x2="14" y2="20" stroke="currentColor" stroke-width="2"/>',
        home: '<rect x="4" y="10" width="16" height="12" fill="none" stroke="currentColor" stroke-width="2"/><path fill="none" stroke="currentColor" stroke-width="2" d="M2 10 L12 2 L22 10"/>'
    };

    /**
     * 创建 SVG 图标元素
     * @param {string} name - 图标名称（不含 icon- 前缀）
     * @param {Object} options - 配置选项
     * @returns {SVGSVGElement}
     */
    function createIcon(name, options) {
        options = options || {};

        const sizeKey = options.size || 'md';
        const size = SIZE_MAP[sizeKey] || SIZE_MAP.md;
        const ariaHidden = options.ariaHidden === true || options.ariaHidden === 'true';

        const svgNs = 'http://www.w3.org/2000/svg';

        const svg = document.createElementNS(svgNs, 'svg');
        svg.setAttribute('width', size);
        svg.setAttribute('height', size);
        svg.setAttribute('viewBox', '0 0 24 24');
        svg.setAttribute('fill', 'none');
        svg.style.display = 'inline-block';
        svg.style.verticalAlign = 'middle';

        if (ariaHidden) {
            svg.setAttribute('aria-hidden', 'true');
        }

        // 使用内联 SVG 内容
        const svgContent = ICONS[name];
        if (svgContent) {
            svg.innerHTML = svgContent;
        }

        return svg;
    }

    /**
     * 替换单个 <i data-icon="..."> 元素
     * @param {HTMLElement} el
     */
    function replaceIcon(el) {
        const name = el.getAttribute('data-icon');
        if (!name) return;

        const size = el.getAttribute('data-size');
        const ariaHidden = el.getAttribute('aria-hidden');
        const id = el.getAttribute('id');

        const svg = createIcon(name, {
            size: size,
            ariaHidden: ariaHidden !== null
        });

        // 保留原始元素的 id
        if (id) {
            svg.setAttribute('id', id);
        }

        el.parentNode.replaceChild(svg, el);
    }

    /**
     * 初始化所有图标
     */
    function initIcons() {
        const icons = document.querySelectorAll('i[data-icon]');
        icons.forEach(replaceIcon);
    }

    /**
     * 观察动态添加的图标
     */
    function observeIcons() {
        if (!window.MutationObserver) return;

        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType !== Node.ELEMENT_NODE) return;

                    if (node.matches && node.matches('i[data-icon]')) {
                        replaceIcon(node);
                    }

                    if (node.querySelectorAll) {
                        const icons = node.querySelectorAll('i[data-icon]');
                        icons.forEach(replaceIcon);
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // 页面加载完成后初始化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initIcons();
            observeIcons();
        });
    } else {
        initIcons();
        observeIcons();
    }

    // 暴露全局 API
    window.BGJQ = window.BGJQ || {};
    window.BGJQ.icons = {
        create: createIcon,
        replace: replaceIcon,
        refresh: initIcons
    };
})();
