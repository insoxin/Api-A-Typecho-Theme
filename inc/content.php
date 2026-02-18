<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 内容处理模块
 * 处理文章内容解析和显示
 */

/**
 * 解析并修改内容
 * 应用 CDN 替换和链接处理
 * @param object $obj 内容对象
 * @return void
 */
function parseContent($obj) {
    $options = Typecho_Widget::widget('Widget_Options');
    
    // CDN 替换（如果配置了）
    if (!empty($options->src_add) && !empty($options->cdn_add)) {
        $obj->content = str_ireplace($options->src_add, $options->cdn_add, $obj->content);
    }
    
    // 为所有链接添加 target="_blank"
    $obj->content = preg_replace("/<a href=\"([^\"]*)\">/i", "<a href=\"\\1\" target=\"_blank\">", $obj->content);
    
    echo trim($obj->content);
}
