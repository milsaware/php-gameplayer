<?php
include_model('app');
include_model('admin');
include_model('chat');
include_model('auth');
include_model('profile');
use appModel as app;
use adminModel as admin;
use chatModel as chat;
use authModel as auth;
use profileModel as profile;
class appController {

	public static function index(){
		$metadata['meta']['title'] = SITENAME;
		$metadata['meta']['description'] = SITENAME;
		
		if(isset($_SESSION['auth']) && isset($_SESSION['uid'])){
			$key = str_replace(' ', '', $_SESSION['auth']);
			$uid = preg_replace('/[^0-9]+/', '', $_SESSION['uid']);
			if($key !== 0 && auth::sessionVerify($uid, $key) == 1){
				$userDetails = auth::fetchUser($uid);
				foreach($userDetails as $row){
					$username = $row['username'];
					$handle = $row['handle'];
				}
				$f1 = $handle[0];
				$f2 = $handle[1];
				$f3 = $handle[2];
				$img = '/assets/images/users/'.$f1.'/'.$f2.'/'.$f3.'/'.$handle.'/';
				$metadata['meta']['img'] = $img;
				$data['img'] = $img;
				$data['username'] = $username;
				$data['handle'] = $handle;
			}

			$gameList = app::fetchAll();
			$likes = profile::fetchLikes($handle);
			$data['liked'] = $likes;
			$data['notLiked'] = (isset($_COOKIE['disliked']))? explode(',', $_COOKIE['disliked']) : array();

			$favArray = (isset($_COOKIE['fav']))? explode(',', 'fav,'.$_COOKIE['fav'].'') : array();
			$data['favList'] = (count($favArray) != 0)? app::fetchWhere($favArray) : '';
			$data['fav'] = $favArray;
		}
		$data['sliderCats'] = app::fetchSliderCats();

		$footdata['copyright'] = '&#169; '.date('Y').' '.SITENAME;

		if(isset($_POST['request'])){
			view::build('welcome', $data);
		}else{
			view::build('head', $metadata).
			view::build('welcome', $data).
			view::build('foot', $footdata);
		}
	}

	public static function game(){
		$metadata['meta']['title'] = SITENAME;
		$metadata['meta']['description'] = SITENAME;
		
		if(isset($_SESSION['auth']) && isset($_SESSION['uid'])){
			$key = str_replace(' ', '', $_SESSION['auth']);
			$uid = preg_replace('/[^0-9]+/', '', $_SESSION['uid']);
			if($key !== 0 && auth::sessionVerify($uid, $key) == 1){
				$userDetails = auth::fetchUser($uid);
				foreach($userDetails as $row){
					$username = $row['username'];
					$handle = $row['handle'];
				}
				$f1 = $handle[0];
				$f2 = $handle[1];
				$f3 = $handle[2];
				$img = '/assets/images/users/'.$f1.'/'.$f2.'/'.$f3.'/'.$handle.'/';
				$metadata['meta']['img'] = $img;
				$data['img'] = $img;
				$data['username'] = $username;
				$data['handle'] = $handle;
			}
		}

		$game_id = preg_replace('/[^0-9]+/', '', $_GET['id']);
		$game = app::fetchGame($game_id);
		foreach($game as $row){
			$author_id = $row['author_id'];
			$data['id'] = $row['id'];
			$data['title'] = $row['title'];
			$data['embedURL'] = urldecode($row['embed']);
			$data['url'] = $row['url'];
		}

		$author = auth::fetchUser($author_id);
		foreach($author as $row){
			$data['author'] = $row['handle'];
			$data['author_url'] = $row['url'];
		}

		$content = '';
		$messages = chat::fetchChat($game_id);
		$commentsRemoved = chat::fetchCommentsRemoved($game_id);
		$crArray = explode(',', $commentsRemoved);
		
		foreach($messages as $row){
			if (!in_array($row['id'], $crArray)){
				$likeclass = (chat::fetchLikes($row['id'], $game_id) == 'false')? 'icon-chat-like icon-thumbs-o-up' :'icon-chat-liked icon-thumbs-up';
				$likeClick = (chat::fetchLikes($row['id'], $game_id) == 'false')? 'commentLike(this)' : 'removeLike(this, '.$game_id.', '.$row['id'].')';

				$respCount = preg_replace('/[^0-9]+/', '', $row['resp_count']);
				$respCountClass = ($respCount < 10)? 'resp_count_s' : (($respCount == 10)? 'resp_count_m' : 'resp_count_l');
				$respCount = ($respCount > 10)? '10+' : $respCount;
				$respCountEl = ($respCount > 0 || $respCount == '10+')? '<span class="'.$respCountClass.'">'.$respCount.'</span>' : '';

				$likeCount = preg_replace('/[^0-9]+/', '', $row['like_count']);
				$likeCountClass = ($likeCount < 10)? 'like_count_s' : (($likeCount == 10)? 'like_count_m' : 'like_count_l');
				$likeCount = ($likeCount > 10)? '10+' : $likeCount;
				$likeCountEl = ($likeCount > 0 || $likeCount == '10+')? '<span class="'.$likeCountClass.'">'.$likeCount.'</span>' : '';
				$message = (strlen($row['message']) > 235)? substr($row['message'], 0, 235).' ...' : $row['message'];

					$content .= '
						<div class="comment_block" data-id="'.$row['id'].'">
							<i class="comment_ell_menu icon-ellipsis-h"></i>
							<div class="comment_head">'.$row['author'].'</div>
							<div class="comment_body">'.$message.'</div>
							<div class="comment_foot">
					';

					$eyeClass = ' eye-left';
					if(isset($_SESSION['auth'])){
						$eyeClass = '';
						$content .= '
							<i class="icon-reply"></i>
							'.$respCountEl.'
							<i class="'.$likeclass.'" onclick="'.$likeClick.'"></i>
							'.$likeCountEl
						;
					}
						
					$content .='
						<i class="icon-eye'.$eyeClass.'"></i>
						<div class="comment_timestamp">'.gmdate("d-m-y H:i:s", $row['timestamp']).'</div>
						</div>
						</div>
					';
				}
		}

		$data['content'] = $content;

		$footdata['copyright'] = '&#169; '.date('Y').' '.SITENAME;

		if(isset($_POST['request'])){
			view::build('game', $data);
		}else{
			view::build('head', $metadata).
			view::build('game', $data).
			view::build('foot', $footdata);
		}
	}

}
