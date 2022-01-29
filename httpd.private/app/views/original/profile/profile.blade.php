<?php

if($page == 'profileSearch'){
	if($games){
		foreach($games as $row){
			echo '<img src="assets/images/games/online/'.$row['id'].'/thumb.png" class="profile_thumb" onclick="openGame(\''.$row['id'].'\')">';
				//echo '<a href="index.php?route=game&id='.$row['id'].'" target="_blank"><img src="assets/images/games/online/'.$row['id'].'/thumb.png" class="profile_thumb"></a>';
			}
		}else{
			echo '<div class="emptyMsg">No matching search</div>';
		}
}

elseif($page == 'profileFetch'){
	$dclass = (isset($_SESSION['auth']))? 'pcbpf' : 'pcbu';
	echo '<div class="'.$dclass.'">';
	if($games){
		foreach($games as $row){
		echo '<img src="assets/images/games/online/'.$row['id'].'/thumb.png" class="profile_thumb" onclick="openGame(\''.$row['id'].'\')">';
		//echo '<a href="index.php?route=game&id='.$row['id'].'" target="_blank"><img src="assets/images/games/online/'.$row['id'].'/thumb.png" class="profile_thumb"></a>';
		}
	}else{
		echo '<div class="emptyMsg">Nothing to see here</div>';
	}
	echo '</div>';
	echo '<script>init(\'profileFetch\');</script>';
}

elseif($page == 'liked'){
	if($likes){
		foreach($likes as $row){
			echo '<img src="assets/images/games/online/'.$row['game_id'].'/thumb.png" class="profile_thumb" onclick="openGame(\''.$row['game_id'].'\')">';
		}
		echo '<script>init(\'profileLiked\');</script>';
	}else{
		echo '<div class="emptyMsg">Nothing to see here</div>';
	}
}

elseif($page == 'following'){
	echo '<div class="profileFollowBlock">';
	if($following){
		foreach($following as $row){
			$handle = $row['handle'];
			echo '<img src="assets/images/users/'.$handle[0].'/'.$handle[1].'/'.$handle[2].'/'.$handle.'/avatar.png" class="pfbAvatar" onclick="openProfile(\''.$handle.'\')">';
		}
	}else{
		echo '<div class="emptyMsg">Nothing to see here</div>';
	}
	echo '</div>';
	echo '<script>init(\'profileFollowing\');</script>';
}

elseif($page == 'settings'){?>
	<div class="profileSettingsBlock">
		<div class="psbOpt">
			<form method="post" action="" enctype="multipart/form-data" id="avatarForm">
				<input type="file" id="avatarIn" accept="image/png" onchange="changeAvatar(this)">
				<label for="avatarIn"><img class="pfbAvatar" src="<?php echo $img;?>avatar.png"></label>
			</form>
		</div>
		<div class="psbOpt">
			<div id="pfbBio" placeholder="enter bio here" oninput="updateBio()" contenteditable><?php echo $profile_content;?></div>
		</div>
			<span class="psbol" onclick="openModal('banner')">Banner</span>
			<span id="changePassword" class="psbol" onclick="login_popup('passRec')">Change password</span>
	</div>
	<script>init('profileSettings');</script>

<?php }elseif($page == 'dashboard'){?>
	Dashboard
	<script>init('profileDashboard');</script>

<?php }elseif($page == 'games'){
	echo '<div class="profileFollowBlock">';
	if($games){
		foreach($games as $row){
			echo '<img src="assets/images/games/online/'.$row['id'].'/thumb.png" class="profile_thumb" onclick="openProfileGame(\''.$row['id'].'\')">';
		}
	}else{
		echo '<div class="emptyMsg">Nothing to see here</div>';
	}
	echo '</div>';
	echo '<script>init(\'profileGames\');</script>';
}

elseif($page == 'nolog'){?>
	<div class="profileSearch">
		<input type="text" class="psIn" placeholder="search profiles">
		<span id="psSubmit">Search</span>
	</div>
<?php }

elseif($page == 'psmoGameNew'){?>
	<form method="post" action="" enctype="multipart/form-data" id="gameThumbForm">
		<input type="file" id="gameThumbIn" accept="image/png" onchange="changeGameThumb(this)">
		<label for="gameThumbIn"><img class="ngThumb" src="assets/images/thumbSelect.png"></label><br>
		<input type="text" id="title" class="newGameIn" placeholder="Title*"><br>
		<textarea id="description" class="newGameTxt" placeholder="Description"></textarea><br>
		<input type="text" id="embedURL" class="newGameIn" placeholder="embed URL*"><br>
		<select id="cat" class="ngSelect">
			<option value="0">Category</option>
			<?php foreach($cat as $row){
				echo '<option value="'.$row['id'].'">'.$row['key'].'</option>';
			}?>
				
		</select><br>
	</form>
	<span id="submitGame" class="btnM" onclick="submitGame()">submit</span>
	<script>init('psmoGameNew');</script>
<?php }

elseif($page == 'psmoSandbox'){
	if($games){
		foreach($games as $row){
		echo '<img src="assets/images/users/'.$handle[0].'/'.$handle[1].'/'.$handle[2].'/'.$handle.'/sandbox/'.$row['id'].'/thumb.png" class="profile_thumb" onclick="openSandboxGame(\''.$row['id'].'\')">';
		}
	}else{
		echo '<div class="emptyMsg">Nothing to see here</div>';
	}
	echo '<script>init(\'profileFetch\');</script>';
}

elseif($page == 'sandbox'){
	foreach($game as $row){
		echo '
		<form method="post" action="" enctype="multipart/form-data" id="gameThumbForm">
		<input type="file" id="gameThumbIn" accept="image/png" onchange="changeGameThumb(this)">
		<label for="gameThumbIn"><img class="sbvThumb" src="assets/images/users/'.$handle[0].'/'.$handle[1].'/'.$handle[2].'/'.$handle.'/sandbox/'.$row['id'].'/thumb.png"></label><br>

		<input type="text" id="title" class="sbvGameIn" placeholder="Title*" value="'.$row['title'].'"><br>

		<textarea id="description" class="sbvGameTxt" placeholder="Description">'.$row['description'].'</textarea><br>

		<input type="text" id="embedURL" class="sbvEmbedIn" placeholder="embed URL*" value="'.urldecode($row['embed']).'"><br>

		<select id="cat" class="sbvSelect">
			<option value="0">Category</option>';
			foreach($cat as $cat_row){
				echo ($cat_row['id'] == $row['cat'])? '<option value="'.$cat_row['id'].'" selected>'.$cat_row['key'].'</option>' : '<option value="'.$cat_row['id'].'">'.$cat_row['key'].'</option>';
			}
				
			echo '
		</select><br>

	</form>
	<span id="sbvSubmit" class="btnM" onclick="submitGame(\'1\', \''.$row['id'].'\')">update</span>';
	echo ($row['req'] == 0)? '<span id="sbvrq" class="btnM" onclick="liveRequest(\''.$row['id'].'\')">live request</span>' : '<span id="sbvrqs" class="btnM">request sent</span>';

	echo '<iframe src="'.urldecode($row['embed']).'" class="sbvPreview" frameborder="0" scrolling="no" allowfullscreen allow="autoplay"></iframe>';
	}
}

elseif($page == 'live'){
	foreach($game as $row){
		echo '
		<form method="post" action="" enctype="multipart/form-data" id="gameThumbForm">
		<input type="file" id="gameThumbIn" accept="image/png" onchange="changeGameThumb(this)">
		<label for="gameThumbIn"><img class="sbvThumb" src="assets/images/games/online/'.$row['game_id'].'/thumb.png"></label><br>

		<input type="text" id="title" class="sbvGameIn" placeholder="Title*" value="'.$row['title'].'"><br>

		<textarea id="description" class="sbvGameTxt" placeholder="Description">'.$row['description'].'</textarea><br>

		<input type="text" id="embedURL" class="sbvEmbedIn" placeholder="embed URL*" value="'.urldecode($row['embed']).'"><br>

		<select id="cat" class="sbvSelect">
			<option value="0">Category</option>';
			foreach($cat as $cat_row){
				echo ($cat_row['id'] == $row['cat'])? '<option value="'.$cat_row['id'].'" selected>'.$cat_row['key'].'</option>' : '<option value="'.$cat_row['id'].'">'.$cat_row['key'].'</option>';
			}
				
			echo '
		</select><br>

	</form>
	<span id="sbvSubmit" class="btnM" onclick="updateLive(\''.$row['game_id'].'\')">update</span>';

	echo '<iframe src="'.urldecode($row['embed']).'" class="sbvPreview" frameborder="0" scrolling="no" allowfullscreen allow="autoplay"></iframe>';
	}
}
?>