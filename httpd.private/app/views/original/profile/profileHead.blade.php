<?php if($page == 'profileFetch'){?>
	<div class="profileBanner" style="background-color: <?php echo $bannerColour;?>"></div>
	<img class="profileAvatar" src="<?php echo $img;?>avatar.png">
	<div class="profileMenu">
		<span class="profileUsername"><?php echo $username;?></span>
		<span class="profileHandle">@<?php echo $handle;?></span>
		<?php echo $followBtn;
		echo '<input type="text" id="profileSearch" class="profileInput" placeholder="search" data-handle="'.$handle.'">';?>
	</div>
<?php }else{?>
<div id="profileHeader">
	<div class="profileBanner" style="background-color: <?php echo $bannerColour;?>"></div>
	<img class="profileAvatar" src="<?php echo $img;?>avatar.png">
	<div class="profileMenu">
		<span class="profileUsername"><?php echo $username;?></span>
		<span class="profileHandle">@<?php echo $handle;?></span>
		<div class="profileMenuOptions">
			<span id="icon-pmoLiked" class="profileMenuOption pmoActive"></span>
			<span id="icon-pmoFollowing" class="profileMenuOption"></span>
			<span id="icon-pmoSettings" class="profileMenuOption"></span>
		</div>
	</div>
</div>

<div id="profileSideMenu">
	<span id="psmo-dashboard" class="psmOption"></span>
	<span id="psmo-games" class="psmOption"></span>
	<span id="psmoGameNew" class="psmSubOption">Upload game</span><p>
	<span id="psmoSandbox" class="psmSubOption">Sandbox</span>
	<span id="psmo-logout" class="psmOption"></span>
</div>
<?php }
echo ($page != 'profileFetch')? '<div class="profileContentBlock">' : '';
?>
