# AI客服插件 - 使用教程

## 版本介绍

### 基础版
- **文件**: AI客服-基础版.html
- **适用**: 任何网站（HTML页面即可）
- **功能**: 简单AI对话

### 专业版
- **文件**: AI客服-专业版.html + AI客服-专业版.php
- **适用**: WordPress网站 / 任何网站
- **功能**: AI对话 + 知识库问答

---

## 基础版使用方法

### 方法1：直接嵌入HTML
1. 下载 `AI客服-基础版.html`
2. 用记事本或代码编辑器打开
3. 找到这行：
   ```javascript
   const API_KEY = 'YOUR_API_KEY'; // 填入客户的API Key
   ```
4. 把 `YOUR_API_KEY` 改成你的API Key
5. 选择模型：
   ```javascript
   const CURRENT_MODEL = 'doubao-seed-2-0-pro-260215'; // 豆包
   // 或
   const CURRENT_MODEL = 'qwen-turbo'; // 千问
   ```
6. 保存文件
7. 把文件上传到你的网站服务器
8. 在网页中引用：
   ```html
   <script src="AI客服-基础版.html"></script>
   ```

### 方法2：WordPress后台
1. 进入WordPress后台
2. 外观 → 小工具 → 添加自定义HTML
3. 粘贴 `AI客服-基础版.html` 的全部内容
4. 修改API Key和模型
5. 保存

---

## 专业版使用方法

### 方法1：HTML直接使用
和基础版一样，下载 `AI客服-专业版.html`，修改API Key即可。

### 方法2：WordPress插件安装
1. 下载 `AI客服-专业版.zip`
2. 进入WordPress后台
3. 插件 → 安装插件 → 上传插件
4. 上传 `AI客服-专业版.zip`
5. 启用插件
6. 在插件设置中配置API Key和模型

---

## API Key获取

### 豆包（免费）
1. 访问 https://www.doubao.com
2. 注册/登录
3. 进入开发者后台
4. 创建API Key
5. 复制并填入

### 千问（收费）
1. 访问 https://dashscope.console.aliyun.com
2. 注册/登录
3. 创建API Key
4. 复制并填入

---

## 常见问题

### Q: 客服不回复怎么办？
A: 检查API Key是否正确，网络是否正常

### Q: 回答很慢怎么办？
A: 可能是API服务器繁忙，尝试切换模型

### Q: 可以自定义客服头像吗？
A: 可以，修改HTML中的SVG图标代码

### Q: 可以修改客服名字吗？
A: 可以，修改SYSTEM_PROMPT中的内容

---

## 技术支持

如有问题，请联系开发者。

---

*最后更新：2026年*
