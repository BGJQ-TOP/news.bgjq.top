# Tasks

## Phase 1: 基础架构重构
- [ ] Task 1: 重构 CSS 变量系统
  - [ ] SubTask 1.1: 定义颜色变量（支持浅色/深色模式）
  - [ ] SubTask 1.2: 定义间距变量
  - [ ] SubTask 1.3: 定义字体和文本大小变量
  - [ ] SubTask 1.4: 定义阴影和圆角变量
  - [ ] SubTask 1.5: 使用 `data-theme` 属性替代 `body.dark-mode/light-mode`

- [ ] Task 2: 创建 SVG 图标系统
  - [ ] SubTask 2.1: 创建 `assets/images/icons.svg` SVG Sprite，包含所有需要的图标
  - [ ] SubTask 2.2: 创建 `assets/js/icons.js` 图标加载脚本
  - [ ] SubTask 2.3: 定义图标使用规范（尺寸、颜色、aria-hidden）

- [ ] Task 3: 优化动画系统
  - [ ] SubTask 3.1: 添加 `prefers-reduced-motion` 媒体查询
  - [ ] SubTask 3.2: 将所有 `transition: all` 替换为具体属性
  - [ ] SubTask 3.3: 确保所有动画仅使用 `transform` 和 `opacity`

## Phase 2: 全局组件优化
- [ ] Task 4: 优化头部导航 (header.php)
  - [ ] SubTask 4.1: 替换主题切换 Emoji 为 SVG 图标
  - [ ] SubTask 4.2: 添加 `aria-label` 到所有 icon-only 按钮
  - [ ] SubTask 4.3: 优化移动端导航栏（汉堡菜单）
  - [ ] SubTask 4.4: 确保搜索框支持键盘导航（Enter 搜索、Esc 清空）
  - [ ] SubTask 4.5: 提取内联样式到 CSS 类

- [ ] Task 5: 优化页脚 (footer.php)
  - [ ] SubTask 5.1: 替换所有 Emoji 为 SVG 图标
  - [ ] SubTask 5.2: 优化链接焦点状态
  - [ ] SubTask 5.3: 确保返回顶部按钮有 `aria-label`

- [ ] Task 6: 优化通用组件样式 (style.css)
  - [ ] SubTask 6.1: 统一按钮样式（高度、圆角、字体、状态）
  - [ ] SubTask 6.2: 统一卡片样式（圆角、阴影、边框、悬停）
  - [ ] SubTask 6.3: 统一表单样式（输入框、标签、错误提示）
  - [ ] SubTask 6.4: 优化焦点状态（使用 `:focus-visible`）
  - [ ] SubTask 6.5: 确保触摸目标最小 44×44px

## Phase 3: 页面级优化
- [ ] Task 7: 优化首页 (home.php)
  - [ ] SubTask 7.1: 替换所有 Emoji 为 SVG 图标
  - [ ] SubTask 7.2: 优化文章卡片布局（网格/列表视图）
  - [ ] SubTask 7.3: 优化分页组件样式
  - [ ] SubTask 7.4: 提取内联样式到 CSS 类
  - [ ] SubTask 7.5: 优化侧边栏组件（热门排行、生态入口、标签、关于）

- [ ] Task 8: 优化文章详情页 (article/detail.php)
  - [ ] SubTask 8.1: 替换所有 Emoji 为 SVG 图标
  - [ ] SubTask 8.2: 优化面包屑导航样式
  - [ ] SubTask 8.3: 优化文章操作按钮（点赞、分享）
  - [ ] SubTask 8.4: 优化相关文章列表
  - [ ] SubTask 8.5: 提取内联样式到 CSS 类

- [ ] Task 9: 优化搜索页 (search.php)
  - [ ] SubTask 9.1: 替换所有 Emoji 为 SVG 图标
  - [ ] SubTask 9.2: 优化搜索结果高亮样式
  - [ ] SubTask 9.3: 优化空状态提示
  - [ ] SubTask 9.4: 提取内联样式到 CSS 类

- [ ] Task 10: 优化分类页 (category.php)
  - [ ] SubTask 10.1: 替换所有 Emoji 为 SVG 图标
  - [ ] SubTask 10.2: 优化栏目信息展示
  - [ ] SubTask 10.3: 优化文章列表布局
  - [ ] SubTask 10.4: 提取内联样式到 CSS 类

- [ ] Task 11: 优化排行榜页 (rankings.php)
  - [ ] SubTask 11.1: 替换所有 Emoji 为 SVG 图标
  - [ ] SubTask 11.2: 优化标签切换按钮样式
  - [ ] SubTask 11.3: 优化排行列表样式
  - [ ] SubTask 11.4: 提取内联样式到 CSS 类

- [ ] Task 12: 优化登录页 (login.php)
  - [ ] SubTask 12.1: 替换所有 Emoji 为 SVG 图标
  - [ ] SubTask 12.2: 添加表单 `autocomplete` 属性
  - [ ] SubTask 12.3: 优化错误提示样式
  - [ ] SubTask 12.4: 提取内联样式到 CSS 类

- [ ] Task 13: 优化投稿页 (contribute.php)
  - [ ] SubTask 13.1: 替换所有 Emoji 为 SVG 图标
  - [ ] SubTask 13.2: 添加表单 `autocomplete` 和 `name` 属性
  - [ ] SubTask 13.3: 优化表单验证提示
  - [ ] SubTask 13.4: 提取内联样式到 CSS 类

- [ ] Task 14: 优化个人中心页 (profile.php)
  - [ ] SubTask 14.1: 替换所有 Emoji 为 SVG 图标
  - [ ] SubTask 14.2: 优化功能卡片布局
  - [ ] SubTask 14.3: 提取内联样式到 CSS 类

## Phase 4: 响应式与性能优化
- [ ] Task 15: 重构响应式样式 (responsive.css)
  - [ ] SubTask 15.1: 统一断点为 480px, 768px, 1024px, 1280px
  - [ ] SubTask 15.2: 优化每个断点下的布局
  - [ ] SubTask 15.3: 优化移动端导航栏
  - [ ] SubTask 15.4: 优化移动端侧边栏
  - [ ] SubTask 15.5: 优化移动端表单布局

- [ ] Task 16: 优化 JavaScript 交互 (main.js)
  - [ ] SubTask 16.1: 优化视图切换功能（支持 reduced motion）
  - [ ] SubTask 16.2: 优化搜索功能（防抖、键盘导航）
  - [ ] SubTask 16.3: 优化图片懒加载
  - [ ] SubTask 16.4: 优化主题切换（支持系统主题检测）
  - [ ] SubTask 16.5: 添加键盘导航支持（Esc 关闭下拉菜单）

## Phase 5: 清理与验证
- [ ] Task 17: 清理冗余代码
  - [ ] SubTask 17.1: 移除 Bootstrap CSS 和 JS 引用
  - [ ] SubTask 17.2: 移除未使用的 CSS 类
  - [ ] SubTask 17.3: 移除未使用的 JavaScript 代码
  - [ ] SubTask 17.4: 检查并移除所有剩余内联样式

- [ ] Task 18: 可访问性验证
  - [ ] SubTask 18.1: 检查所有图片是否有 `alt` 属性
  - [ ] SubTask 18.2: 检查所有 icon-only 按钮是否有 `aria-label`
  - [ ] SubTask 18.3: 检查所有表单输入是否有 `<label>`
  - [ ] SubTask 18.4: 检查焦点状态是否可见
  - [ ] SubTask 18.5: 检查键盘导航是否完整

- [ ] Task 19: 跨浏览器测试
  - [ ] SubTask 19.1: 测试 Chrome/Edge
  - [ ] SubTask 19.2: 测试 Firefox
  - [ ] SubTask 19.3: 测试 Safari
  - [ ] SubTask 19.4: 测试移动端 Safari (iOS)
  - [ ] SubTask 19.5: 测试移动端 Chrome (Android)

# Task Dependencies
- Task 1 (CSS 变量) 必须在 Task 4-14 之前完成
- Task 2 (SVG 图标) 必须在 Task 4-14 之前完成
- Task 3 (动画优化) 必须在 Task 4-14 之前完成
- Task 4 (header) 和 Task 5 (footer) 可以并行
- Task 7-14 (页面优化) 可以并行，但依赖 Task 1-3
- Task 15 (响应式) 依赖 Task 4-14
- Task 16 (JS) 依赖 Task 4-5
- Task 17-19 (清理验证) 依赖所有其他任务
