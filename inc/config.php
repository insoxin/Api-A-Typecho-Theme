<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 主题配置模块
 * 包含主题配置函数和配置验证
 */

/**
 * 主题配置函数
 * @param object $form 表单对象
 * @return void
 */
function themeConfig($form) {
    // 博主QQ号码
    $qq = new Typecho_Widget_Helper_Form_Element_Text(
        'qq', 
        NULL, 
        '', 
        _t('博主QQ'), 
        _t('在这里填入博主的QQ号码，如：192666378')
    );
    $qq->input->setAttribute('class', 'w-50');
    $form->addInput($qq);
    
    // 博主微博平台
    $weiboz = new Typecho_Widget_Helper_Form_Element_Text(
        'weiboz', 
        NULL, 
        '//weibo.com', 
        _t('博主所属微博'), 
        _t('在这里填入博主所属微博平台，新浪填写 //weibo.com')
    );
    $form->addInput($weiboz);
    
    // 博主微博ID
    $weibo = new Typecho_Widget_Helper_Form_Element_Text(
        'weibo', 
        NULL, 
        '', 
        _t('博主微博ID'), 
        _t('在这里填入博主的微博ID')
    );
    $form->addInput($weibo);
    
    // 关于页面展示图
    $tt = new Typecho_Widget_Helper_Form_Element_Text(
        'tt', 
        NULL, 
        'https://api.isoyu.com/mm_images.php', 
        _t('关于界面展示图'), 
        _t('填入图片地址，建议图片高度 158px')
    );
    $form->addInput($tt);
    
    // 缩略图设置选项
    $slimg = new Typecho_Widget_Helper_Form_Element_Select(
        'slimg', 
        array(
            'showon' => '有图文章显示缩略图，无图文章随机显示缩略图',
            'Showimg' => '有图文章显示缩略图，无图文章只显示一张固定的缩略图',      
            'showoff' => '有图文章显示缩略图，无图文章则不显示缩略图',
            'allsj' => '所有文章一律显示随机缩略图',
            'guanbi' => '关闭所有缩略图显示'
        ), 
        'guanbi',
        _t('缩略图设置'), 
        _t('默认选择"关闭所有缩略图显示"')
    );
    $form->addInput($slimg->multiMode());
    
    // 启用缓存
    $enableCache = new Typecho_Widget_Helper_Form_Element_Radio(
        'enableCache',
        array(
            '1' => _t('启用'),
            '0' => _t('禁用')
        ),
        '1',
        _t('启用缓存'),
        _t('启用后将缓存文章浏览量、缩略图等数据，提升性能')
    );
    $form->addInput($enableCache);
    
    // 缓存时间
    $cacheTime = new Typecho_Widget_Helper_Form_Element_Text(
        'cacheTime',
        NULL,
        '3600',
        _t('缓存时间（秒）'),
        _t('设置缓存过期时间，默认 3600 秒（1小时）')
    );
    $form->addInput($cacheTime);
    
    // CDN 域名
    $cdnUrl = new Typecho_Widget_Helper_Form_Element_Text(
        'cdnUrl',
        NULL,
        '',
        _t('CDN 加速域名'),
        _t('填入CDN域名，如：https://cdn.example.com，留空则不启用')
    );
    $form->addInput($cdnUrl);
    
    // 图片懒加载
    $lazyLoad = new Typecho_Widget_Helper_Form_Element_Radio(
        'lazyLoad',
        array(
            '1' => _t('启用'),
            '0' => _t('禁用')
        ),
        '1',
        _t('图片懒加载'),
        _t('启用后图片将在进入可视区域时才加载，提升页面加载速度')
    );
    $form->addInput($lazyLoad);
    
    // 统计代码
    $tongji = new Typecho_Widget_Helper_Form_Element_Textarea(
        'tongji', 
        NULL, 
        '', 
        _t('统计代码'), 
        _t('在这里填入第三方统计代码，如百度统计、Google Analytics等')
    );
    $form->addInput($tongji);
}

/**
 * 配置验证器
 */
class API_Config_Validator {
    /**
     * 验证 QQ 号
     * @param string $qq QQ号
     * @return bool
     */
    public static function validateQQ($qq) {
        if (empty($qq)) {
            return true; // 允许为空
        }
        // QQ号为5-12位数字，首位不为0
        return preg_match('/^[1-9][0-9]{4,11}$/', $qq);
    }
    
    /**
     * 验证微博 URL
     * @param string $url 微博URL
     * @return bool
     */
    public static function validateWeiboUrl($url) {
        if (empty($url)) {
            return true;
        }
        return preg_match('/^(https?:)?\/\/(weibo\.com|t\.qq\.com)/', $url);
    }
    
    /**
     * 验证图片 URL
     * @param string $url 图片URL
     * @return bool
     */
    public static function validateImageUrl($url) {
        if (empty($url)) {
            return true;
        }
        // 验证是否为有效的URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        // 验证是否以图片扩展名结尾
        return preg_match('/\.(jpg|jpeg|png|gif|webp|bmp|svg)$/i', $url) ||
               // 或者是已知的图片API域名
               preg_match('/^https?:\/\/(api|cdn|img)\./i', $url);
    }
    
    /**
     * 验证 CDN 域名
     * @param string $url CDN域名
     * @return bool
     */
    public static function validateCdnUrl($url) {
        if (empty($url)) {
            return true;
        }
        return filter_var($url, FILTER_VALIDATE_URL) && 
               (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0);
    }
    
    /**
     * 验证缓存时间
     * @param string $time 缓存时间（秒）
     * @return bool
     */
    public static function validateCacheTime($time) {
        return is_numeric($time) && $time >= 0 && $time <= 86400;
    }
}
