# Checklist

## 基础架构
- [ ] CSS 变量系统已定义（颜色、间距、字体、阴影）
- [ ] 支持浅色/深色模式切换（使用 `data-theme`）
- [ ] SVG 图标系统已创建（icons.svg + icons.js）
- [ ] `prefers-reduced-motion` 媒体查询已添加
- [ ] 所有 `transition: all` 已替换为具体属性
- [ ] 所有动画仅使用 `transform` 和 `opacity`

## 全局组件
- [ ] 头部导航 (header.php) 中所有 Emoji 已替换为 SVG 图标
- [ ] 头部导航中所有 icon-only 按钮有 `aria-label`
- [ ] 搜索框支持 Enter 搜索和 Esc 清空
- [ ] 移动端导航栏有汉堡菜单
- [ ] 页脚 (footer.php) 中所有 Emoji 已替换为 SVG 图标
- [ ] 返回顶部按钮有 `aria-label`
- [ ] 按钮样式统一（高度、圆角、字体、状态）
- [ ] 卡片样式统一（圆角、阴影、边框、悬停）
- [ ] 表单样式统一（输入框、标签、错误提示）
- [ ] 焦点状态使用 `:focus-visible`
- [ ] 触摸目标最小 44×44px

## 页面级优化
- [ ] 首页 (home.php) 中所有 Emoji 已替换为 SVG 图标
- [ ] 首页文章卡片布局优化（网格/列表视图）
- [ ] 首页分页组件样式优化
- [ ] 首页侧边栏组件优化
- [ ] 文章详情页 (article/detail.php) 中所有 Emoji 已替换为 SVG 图标
- [ ] 文章详情页面包屑导航样式优化
- [ ] 文章详情页操作按钮（点赞、分享）优化
- [ ] 搜索页 (search.php) 中所有 Emoji 已替换为 SVG 图标
- [ ] 搜索页搜索结果高亮样式优化
- [ ] 分类页 (category.php) 中所有 Emoji 已替换为 SVG 图标
- [ ] 排行榜页 (rankings.php) 中所有 Emoji 已替换为 SVG 图标
- [ ] 登录页 (login.php) 中所有 Emoji 已替换为 SVG 图标
- [ ] 登录页表单有 `autocomplete` 属性
- [ ] 投稿页 (contribute.php) 中所有 Emoji 已替换为 SVG 图标
- [ ] 投稿页表单有 `autocomplete` 和 `name` 属性
- [ ] 个人中心页 (profile.php) 中所有 Emoji 已替换为 SVG 图标

## 响应式与性能
- [ ] 响应式断点统一为 480px, 768px, 1024px, 1280px
- [ ] 移动端导航栏折叠正常
- [ ] 移动端侧边栏移至主内容下方
- [ ] 移动端表单布局正常
- [ ] 视图切换功能支持 reduced motion
- [ ] 搜索功能有防抖
- [ ] 图片懒加载正常工作
- [ ] 主题切换支持系统主题检测
- [ ] 键盘导航支持（Esc 关闭下拉菜单）

## 清理与验证
- [ ] Bootstrap CSS 和 JS 引用已移除
- [ ] 未使用的 CSS 类已移除
- [ ] 未使用的 JavaScript 代码已移除
- [ ] 所有内联样式已提取到 CSS 类
- [ ] 所有图片有 `alt` 属性
- [ ] 所有 icon-only 按钮有 `aria-label`
- [ ] 所有表单输入有 `<label>`
- [ ] 焦点状态可见
- [ ] 键盘导航完整
- [ ] Chrome/Edge 测试通过
- [ ] Firefox 测试通过
- [ ] Safari 测试通过
- [ ] 移动端 Safari (iOS) 测试通过
- [ ] 移动端 Chrome (Android) 测试通过
