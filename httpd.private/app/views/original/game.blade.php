<div class="game_column">
<embed src="<?php echo $embedURL;?>" frameborder="0" scrolling="no" allow="autoplay" height="100%">
</div>

<div id="chat_column"><?php echo $content;?></div>

<div class="comment_in">
<?php if(isset($_SESSION['auth'])){?>
	<textarea id="comment_text" placeholder="Write message here ..."></textarea>
	<span id="new_comment_submit">send</span>
<?php }else{?>
	<div class="login_container">
		<span id="chatSignin" class="click">Sign in</span>&nbsp;or&nbsp;<span id="chatRegister" class="click">sign up</span>&nbsp;to chat
	</div>
<?php }?>
</div>

<div class="game_info_column">
	<span class="game_info_head"><?php echo $title;?></span>
	<a class="game_info_author" href="<?php echo '/profile/'.$author;?>"><?php echo $author;?></a>

	<span id="gameSettings" class="icon-cog"></span>
	
</div>
<input type="hidden" id="game_id" value="<?php echo $id;?>">
<script>gameIni();</script>
