<?php
/**
 * Plugin Name: AI客服小助手
 * Description: 基于DeepSeek AI的智能客服插件
 * Version: 2.0
 * Author: 寻川
 */

if (!defined('ABSPATH')) exit;

// 设置选项
register_activation_hook(__FILE__, 'aichat_install');
function aichat_install() {
    add_option('aichat_api_key', '');
    add_option('aichat_welcome', '你好！我是AI客服，有什么可以帮你的？');
    add_option('aichat_system_prompt', '');
}

add_action('admin_menu', 'aichat_menu');
function aichat_menu() {
    add_options_page('AI客服设置', 'AI客服', 'manage_options', 'ai-chat', 'aichat_options');
}

function aichat_options() {
    if (isset($_POST['aichat_save'])) {
        update_option('aichat_api_key', sanitize_text_field($_POST['api_key']));
        update_option('aichat_welcome', sanitize_textarea_field($_POST['welcome']));
        update_option('aichat_system_prompt', sanitize_textarea_field($_POST['system_prompt']));
        echo '<div class="updated"><p>保存成功！</p></div>';
    }
    $api_key = get_option('aichat_api_key', '');
    $welcome = get_option('aichat_welcome', '你好！我是AI客服，有什么可以帮你的？');
    $system_prompt = get_option('aichat_system_prompt', '');
    ?>
    <div class="wrap">
        <h1>AI客服设置</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th>DeepSeek API Key</th>
                    <td><input type="text" name="api_key" value="<?php echo esc_attr($api_key); ?>" size="60" placeholder="sk-xxx"></td>
                </tr>
                <tr>
                    <th>欢迎语</th>
                    <td><textarea name="welcome" rows="2" cols="60"><?php echo esc_textarea($welcome); ?></textarea></td>
                </tr>
                <tr>
                    <th>行业提示词（系统提示）</th>
                    <td>
                        <textarea name="system_prompt" rows="8" cols="60" placeholder="设置AI的角色和行为规则，例如：你是XX电脑店的客服..."><?php echo esc_textarea($system_prompt); ?></textarea>
                        <p><strong>预设模板：</strong></p>
                        <p><a href="?page=ai-chat&preset=computer">💻 电脑店</a> | <a href="?page=ai-chat&preset=repair">🔧 维修店</a> | <a href="?page=ai-chat&preset=shop">🏪 便利店</a></p>
                    </td>
                </tr>
            </table>
            <p><a href="https://platform.deepseek.com/" target="_blank">获取DeepSeek API Key</a></p>
            <p class="submit"><input type="submit" name="aichat_save" class="button-primary" value="保存"></p>
        </form>
        
        <h2>使用方法</h2>
        <ol>
            <li>在页面或文章中添加短代码 <code>[ai_chat]</code> 显示聊天窗口</li>
            <li>或者在主题的footer.php中添加 <code>&lt;?php do_action('aichat_widget'); ?&gt;</code></li>
        </ol>
        
        <h2>行业提示词示例</h2>
        <h3>电脑店：</h3>
        <pre style="background:#f5f5f5;padding:10px;">你是XX电脑店的AI客服。你需要：
1. 熟悉店内产品：联想ThinkPad X1笔记本￥5999，戴尔灵越14寸￥4599，组装机可根据需求推荐
2. 了解优惠政策：学生优惠95折，会员积分抵现
3. 回答问题要专业、热情
4. 如果客户问的产品不在你的知识范围内，建议到店咨询或电话联系</pre>
        
        <h3>维修店：</h3>
        <pre style="background:#f5f5f5;padding:10px;">你是XX电脑维修店的AI客服。主要服务：
1. 电脑维修：系统重装50元，清灰30元，硬件更换费用另计
2. 网络调试：路由器设置100元，网线布线
3. 数据恢复：按难度报价，500元起
4. 回答要简洁，告知客户到店或预约上门服务</pre>
    </div>
    <?php
}

// 处理预设
add_action('init', 'aichat_preset');
function aichat_preset() {
    if (isset($_GET['page']) && $_GET['page'] == 'ai-chat' && isset($_GET['preset'])) {
        $presets = array(
            'computer' => '你是XX电脑店的AI客服。你需要：熟悉店内产品（联想ThinkPad X1笔记本￥5999，戴尔灵越14寸￥4599）、了解优惠政策（学生优惠95折，会员积分抵现）、回答问题要专业热情。如果客户问的产品不在知识范围内，建议到店咨询。',
            'repair' => '你是XX电脑维修店的AI客服。主要服务：电脑维修（系统重装50元，清灰30元）、网络调试（路由器设置100元）、数据恢复（500元起）。回答要简洁，告知客户到店或预约上门服务。',
            'shop' => '你是XX便利店的AI客服。主营：日用品、零食、饮料、烟酒。营业时间：早7点-晚11点。支持电话订货，满50元送货上门。回答要热情周到。'
        );
        if (isset($presets[$_GET['preset']])) {
            update_option('aichat_system_prompt', $presets[$_GET['preset']]);
            wp_redirect('?page=ai-chat');
            exit;
        }
    }
}

// 短代码
add_shortcode('ai_chat', 'aichat_shortcode');
function aichat_shortcode() {
    ob_start();
    aichat_widget_html();
    return ob_get_clean();
}

// 页脚自动显示
add_action('wp_footer', 'aichat_auto_show');
function aichat_auto_show() {
    $api_key = get_option('aichat_api_key', '');
    if ($api_key) {
        aichat_widget_html();
    }
}

function aichat_widget_html() {
    $api_key = get_option('aichat_api_key', '');
    $welcome = get_option('aichat_welcome', '你好！我是AI客服，有什么可以帮你的？');
    $system_prompt = get_option('aichat_system_prompt', '你是网站的AI客服，友好专业地回答问题');
    if (!$api_key) return;
    ?>
    <style>
    .ai-chat-widget{position:fixed;bottom:20px;right:20px;z-index:9999;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif}
    .ai-chat-toggle{width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);box-shadow:0 4px 15px rgba(102,126,234,0.4);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:transform .3s}
    .ai-chat-toggle:hover{transform:scale(1.1)}
    .ai-chat-toggle svg{width:30px;height:30px;fill:#fff}
    .ai-chat-box{position:absolute;bottom:80px;right:0;width:350px;height:450px;background:#fff;border-radius:16px;box-shadow:0 10px 40px rgba(0,0,0,.15);display:none;flex-direction:column;overflow:hidden}
    .ai-chat-box.active{display:flex}
    .ai-chat-header{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:16px;font-weight:600;display:flex;align-items:center;gap:10px}
    .ai-chat-header .avatar{width:36px;height:36px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center}
    .ai-chat-header .avatar svg{width:24px;height:24px;fill:#667eea}
    .ai-chat-messages{flex:1;padding:15px;overflow-y:auto;background:#f8f9fa}
    .ai-message{background:#fff;padding:12px;border-radius:12px 12px 12px 4px;margin-bottom:10px;max-width:85%;box-shadow:0 2px 5px rgba(0,0,0,.05);font-size:14px;line-height:1.5}
    .user-message{background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;border-radius:12px 12px 4px 12px;margin-left:auto;margin-bottom:10px;max-width:85%;padding:12px;font-size:14px;line-height:1.5}
    .ai-chat-input{padding:12px;border-top:1px solid #eee;display:flex;gap:8px;background:#fff}
    .ai-chat-input input{flex:1;padding:10px 14px;border:1px solid #ddd;border-radius:20px;outline:none;font-size:14px}
    .ai-chat-input input:focus{border-color:#667eea}
    .ai-chat-input button{width:40px;height:40px;border-radius:50%;border:none;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center}
    .ai-chat-input button:disabled{opacity:.6}
    .typing-indicator{display:flex;gap:4px;padding:12px;background:#fff;border-radius:12px 12px 12px 4px;margin-bottom:10px}
    .typing-indicator span{width:8px;height:8px;border-radius:50%;background:#667eea;animation:typing 1.4s infinite}
    .typing-indicator span:nth-child(2){animation-delay:.2s}
    .typing-indicator span:nth-child(3){animation-delay:.4s}
    @keyframes typing{0%,100%{transform:translateY(0);opacity:.5}50%{transform:translateY(-5px);opacity:1}}
    </style>
    <div class="ai-chat-widget">
    <div class="ai-chat-box" id="aiChatBox">
        <div class="ai-chat-header">
            <div class="avatar"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></div>
            <span>AI 客服</span>
        </div>
        <div class="ai-chat-messages" id="aiMessages"><div class="ai-message"><?php echo esc_html($welcome); ?></div></div>
        <div class="ai-chat-input">
            <input type="text" id="aiInput" placeholder="输入消息..." onkeypress="if(event.key==='Enter')sendMessage()">
            <button id="aiSendBtn" onclick="sendMessage()">➤</button>
        </div>
    </div>
    <div class="ai-chat-toggle" onclick="toggleChat()">
        <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
    </div>
    </div>
    <script>
    const API_KEY = '<?php echo esc_js($api_key); ?>';
    const API_URL = 'https://api.deepseek.com/chat/completions';
    const SYSTEM_PROMPT = '<?php echo esc_js($system_prompt); ?>';
    let isChatOpen = false, isTyping = false;
    function toggleChat(){isChatOpen=!isChatOpen;document.getElementById('aiChatBox').classList.toggle('active',isChatOpen);}
    function addMessage(content,isUser){const m=document.getElementById('aiMessages');const d=document.createElement('div');d.className=isUser?'user-message':'ai-message';d.textContent=content;m.appendChild(d);m.scrollTop=m.scrollHeight;}
    function showTyping(){const m=document.getElementById('aiMessages');const d=document.createElement('div');d.className='typing-indicator';d.id='typing';d.innerHTML='<span></span><span></span><span></span';m.appendChild(d);m.scrollTop=m.scrollHeight;}
    function hideTyping(){document.getElementById('typing')?.remove();}
    async function sendMessage(){const i=document.getElementById('aiInput'),b=document.getElementById('aiSendBtn');const v=i.value.trim();if(!v||isTyping)return;addMessage(v,!0);i.value='';isTyping=!0;b.disabled=!0;showTyping();try{const r=await fetch(API_URL,{method:'POST',headers:{'Content-Type':'application/json','Authorization':'Bearer '+API_KEY},body:JSON.stringify({model:'deepseek-chat',messages:[{role:'system',content:SYSTEM_PROMPT},{role:'user',content:v}],max_tokens:500})});const d=await r.json();hideTyping();addMessage(d.choices?.[0]?.message?.content||'抱歉出错了',!1)}catch(e){hideTyping();addMessage('网络错误',!1)}isTyping=!1;b.disabled=!1}
    </script>
    <?php
}
