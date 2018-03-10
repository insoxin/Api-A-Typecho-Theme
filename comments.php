 <?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
        
                   <div id="comments">
    <?php $this->comments()->to($comments); ?>
    <?php if ($comments->have()): ?>
	<h4 class="m-t-lg m-b"><?php $this->commentsNum(_t('暂无评论'), _t('仅有一条评论'), _t('已有 %d 条评论')); ?></h4>

   <?php $comments->listComments(); ?>
    <?php $comments->pageNav('&laquo; ', '&raquo;'); ?>
    
    <?php endif; ?>

    <?php if($this->allow('comment')): ?>
    <div id="<?php $this->respondId(); ?>" class="respond">
        <div class="cancel-comment-reply">
        <?php $comments->cancelReply(); ?>
        </div>
    
    	<h4 class="m-t-lg m-b"><?php _e('添加新评论'); ?></h4>
    	<form id="comment_form" method="post" action="<?php $this->commentUrl() ?>" role="form">
  <?php if($this->user->hasLogin()): ?>
    		<p><?php _e('登录身份: '); ?><a href="<?php $this->options->profileUrl(); ?>"><?php $this->user->screenName(); ?></a>. <a href="<?php $this->options->logoutUrl(); ?>" title="Logout"><?php _e('退出'); ?> &raquo;</a></p>
            <?php else: ?>
                    <div class="form-group pull-in clearfix">
                      <div class="col-sm-6">
                        <label>称呼</label>
                        <input type="text" name="author" id="author"  class="form-control">
                      </div>
                      <div class="col-sm-6">
                        <label >邮箱</label>
                        <input type="email" name="mail" id="mail"  class="form-control" value="<?php $this->remember('mail'); ?>"<?php if ($this->options->commentsRequireMail): ?> required<?php endif; ?>>
                      </div>
                    
<div class="col-sm-6">
                        <label>网站</label>
	<input type="url" name="url" id="url"  class="form-control"placeholder="<?php _e('https://'); ?>" value="<?php $this->remember('url'); ?>"<?php if ($this->options->commentsRequireURL): ?> required<?php endif; ?> />
                        </div> 
                      </div>
<input type="checkbox" name="receiveMail" id="receiveMail" value="yes" checked /> <label for="receiveMail" style="padding-left:8px;">当有人回复时接收邮件提醒</label>
<?php endif; ?>
                    <div class="form-group">
                   
                      <textarea class="form-control" rows="5"cols="50" name="text" id="textarea" class="textarea" onkeydown="if(event.ctrlKey&&event.keyCode==13){document.getElementById('misubmit').click();return false};" required ><?php $this->remember('text'); ?></textarea>
                    </div>
                    <div class="form-group">
                      <button class="btn btn-s-md btn-info btn-rounded" type="submit">提交</button>
                    </div>
                  </form>
                </div>
  
    <?php else: ?>
    <h3><?php _e('评论已关闭'); ?></h3>
    <?php endif; ?> 


</div> 