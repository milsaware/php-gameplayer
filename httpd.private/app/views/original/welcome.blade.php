<?php
$i = 0;
echo '<div id="homeScreen">';
foreach($sliderCats as $sliderCatsarray){
//if($sliderCatsarray['id'] < 5 ){
	$slideList = array(adminModel::fetchHome($sliderCatsarray['id']));
	foreach($slideList as $array){
		if($array != ''){
			echo '
				<div class="homeCatSect" data="'.$sliderCats[$i]['key'].'">
					<span class="homeCatHead">'.$sliderCats[$i]['key'].'</span>

					<div class="sBtn">
						<div class="slideLeft">
							<span class="icon-chevron-left sliderCarat"></span>
						</div>
						<div class="slideRight">
							<span class="icon-chevron-right sliderCarat"></span>
						</div>
					</div>
					<div class="slider">
			';

			foreach($array as $row){
				if(isset($_SESSION['auth']) && isset($_SESSION['uid'])){
					$dataLiked = (appModel::fetchGameLikes($row['id']) == 'false')? ' icon-thumbs-o-up' :' icon-thumbs-up';

					$dataLiked = (appModel::fetchGameLikes($row['id']) == 'true')? 1 : 0;
					$dataFav = (appModel::fetchGameLikes($row['id']) == 'true')? 1 : 0;
					echo '
						<div class="slider_block" data="'.$row['id'].'" dataliked="'.$dataLiked.'" datafav="'.$dataFav.'">
							<img src="assets/images/games/online/'.$row['id'].'/thumb.png" class="slider_thumb">
						</div>
					';
				}else{
					echo '
						<div class="slider_block" data="'.$row['id'].'">
							<img src="assets/images/games/online/'.$row['id'].'/thumb.png" class="slider_thumb">
						</div>
					';
				}
			}
			
			echo '</div>';
			echo '<span class="count" data="0"></span></div>';

				$i++;
		}
	}
}
//}

echo '</div>';

echo '<script>sliderIni();</script>';
