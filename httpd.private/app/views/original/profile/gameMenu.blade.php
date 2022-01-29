<?php
if($page == 'general'){?>
<div class="modal_title" placeholder="Game title*" contenteditable><?php echo $game_title;?></div>
<?php
if(isset($game_description)){?>
<div class="modal_description" placeholder="Game description" contenteditable><?php echo $game_description;?></div>
<?php }
}elseif($page == 'stats'){?>
<span id="sopimp" class="statsOpt">Impressions: <?php echo $imp;?></span>
<span id="sophits" class="statsOpt">Hits: <?php echo $hits;?></span>
<span id="soplikes" class="statsOpt">Likes: <?php echo $likes;?></span>
<span id="sopdownloads" class="statsOpt">Downloads: <?php echo $downloads;?></span>

<?php }elseif($page == 'update'){?>
<span class="mdbHead">Update embed URL</span>
<input type="text" class="updateIn" placeholder="embed link" value="<?php echo $game_embed;?>">
<span id="emupConfirm" class="mdbdBtn">update</span>

<?php }elseif($page == 'sell'){?>
<span class="mdbHead">List your game in the marketplace</span>
<label id="ugl" for="uploadGame">Click to upload file</label>
<input type="file" id="uploadGame" onchange="console.log(this.value)" hidden>

<?php }elseif($page == 'delete'){?>
<span id="mdbDeleteHead">Are you sure you want to delete?</span>
<div class="mdbdBtnBlock">
	<span id="mdbDeleteConf" class="mdbdBtn">confirm</span>
	<span id="mdbDeleteCancel" class="mdbdBtn">cancel</span>
</div>

<?php }
