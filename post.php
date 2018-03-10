<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
	<!-- END #fh5co-header -->

	<div class="container-fluid">

		<div class="row fh5co-post-entry single-entry">
<article class="col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-12 col-xs-offset-0">
				<span class="fh5co-meta animate-box"><?php $this->category(','); ?></span>

				<h2 class="fh5co-article-title animate-box"><?php $this->title() ?></h2>

				read(<?php get_post_view($this) ?>)<?php echo art_count($this->cid); ?>汉字（UTF-8）<span class="fh5co-meta fh5co-date animate-box"><?php $this->date('F j, Y'); ?></span>

				

				<div class="col-lg-12 col-lg-offset-0 col-md-12 col-md-offset-0 col-sm-12 col-sm-offset-0 col-xs-12 col-xs-offset-0 text-left content-article">

					<div class="row">
<?php parseContent($this); ?>
		<p class="post-info">
			本文由 <a href="<?php $this->author->permalink(); ?>"><?php $this->author() ?></a> 创作，采用 <a href="https://creativecommons.org/licenses/by/4.0/" target="_blank" rel="external nofollow">知识共享署名4.0</a> 国际许可协议进行许可<br>本站文章除注明转载/出处外，均为本站原创或翻译，转载前请务必署名<br>最后编辑时间为: <?php echo date('M j, Y \\a\t h:i a' , $this->modified); ?>
		</p>
	</div>
</div>
			</article>
		</div>
<style>
.img-responsive {
  display: block;
  max-width: 100%;
  height: auto;
}</style>
<?php $this->need('comments.php'); ?>

<?php $this->need('footer.php'); ?>