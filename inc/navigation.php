<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 导航功能模块
 * 包含上一篇、下一篇文章导航
 */

/**
 * 显示下一篇文章链接
 * @param object $widget Widget 对象
 * @param string $default 默认文本
 * @return void
 */
function theNext($widget, $default = NULL) {
    $db = Typecho_Db::get();
    $sql = $db->select()->from('table.contents')
        ->where('table.contents.created > ?', $widget->created)
        ->where('table.contents.status = ?', 'publish')
        ->where('table.contents.type = ?', $widget->type)
        ->where('table.contents.password IS NULL')
        ->order('table.contents.created', Typecho_Db::SORT_ASC)
        ->limit(1);
    
    $content = $db->fetchRow($sql);
    
    if ($content) {
        $content = $widget->filter($content);
        $title = htmlspecialchars($content['title'], ENT_QUOTES, 'UTF-8');
        $link = '<a href="' . $content['permalink'] . '" title="' . $title . '">←</a>';
        echo $link;
    } else {
        echo $default;
    }
}

/**
 * 显示上一篇文章链接
 * @param object $widget Widget 对象
 * @param string $default 默认文本
 * @return void
 */
function thePrev($widget, $default = NULL) {
    $db = Typecho_Db::get();
    $sql = $db->select()->from('table.contents')
        ->where('table.contents.created < ?', $widget->created)
        ->where('table.contents.status = ?', 'publish')
        ->where('table.contents.type = ?', $widget->type)
        ->where('table.contents.password IS NULL')
        ->order('table.contents.created', Typecho_Db::SORT_DESC)
        ->limit(1);
    
    $content = $db->fetchRow($sql);
    
    if ($content) {
        $content = $widget->filter($content);
        $title = htmlspecialchars($content['title'], ENT_QUOTES, 'UTF-8');
        $link = '<a href="' . $content['permalink'] . '" title="' . $title . '">→</a>';
        echo $link;
    } else {
        echo $default;
    }
}
