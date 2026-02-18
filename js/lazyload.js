/**
 * 图片懒加载实现
 * 使用 Intersection Observer API 实现高性能懒加载
 */
(function() {
    'use strict';
    
    // 检查浏览器是否支持 Intersection Observer
    if (!('IntersectionObserver' in window)) {
        // 降级处理：直接加载所有图片
        loadAllImages();
        return;
    }
    
    // 创建 Intersection Observer
    var imageObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                var img = entry.target;
                loadImage(img);
                observer.unobserve(img);
            }
        });
    }, {
        rootMargin: '50px 0px', // 提前 50px 开始加载
        threshold: 0.01
    });
    
    /**
     * 加载单个图片
     */
    function loadImage(img) {
        var src = img.getAttribute('data-src');
        if (!src) {
            return;
        }
        
        img.src = src;
        img.classList.remove('lazyload');
        img.classList.add('lazyloaded');
    }
    
    /**
     * 降级处理：加载所有图片
     */
    function loadAllImages() {
        var images = document.querySelectorAll('img.lazyload');
        // 兼容旧浏览器，使用传统循环
        for (var i = 0; i < images.length; i++) {
            loadImage(images[i]);
        }
    }
    
    /**
     * 初始化懒加载
     */
    function init() {
        var lazyImages = document.querySelectorAll('img.lazyload');
        // 兼容旧浏览器，使用传统循环
        for (var i = 0; i < lazyImages.length; i++) {
            imageObserver.observe(lazyImages[i]);
        }
    }
    
    // DOM 加载完成后初始化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
