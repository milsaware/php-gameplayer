<?php
include_model('auth');
include_model('admin');
include_model('chat');
include_model('form');
include_model('profile');
include_model('app');
use appModel as app;
use authModel as auth;
use profileModel as profile;
use formModel as form;
use adminModel as admin;
use chatModel as chat;
class formController {

	public static function index(){
		if(isset($_GET['form'])){
			if($_GET['form'] == 'cookie_accept'){
				$type = preg_replace('/[^a-z_]+/', '', $_POST['type']);
				$id = preg_replace('/[^0-9]+/', '', $_POST['id']);
				formController::cookie_set($type, $id);

				if($type != 'fav'){
					$undo_type = ($type == 'like')? 'dislike' : 'like';
					formController::undo_like($undo_type, $id);
				}
				die();
			}

			elseif($_GET['form'] == 'fetchGameLike'){
				$game_id = preg_replace('/[^0-9]+/', '', $_POST['game_id']);
				echo app::fetchGameLikes($game_id);
			}

			elseif($_GET['form'] == 'preference'){
				$type = preg_replace('/[^a-z_]+/', '', $_POST['type']);
				$id = preg_replace('/[^0-9]+/', '', $_POST['id']);
				$action = preg_replace('/[^A-Za-z]+/', '', $_GET['action']);
				if($action == 'setFav'){
					app::setFav($id, $type);
				}
			}

			elseif($_GET['form'] == 'removeComment'){
				$comment_id = preg_replace('/[^0-9]+/', '', $_POST['id']);
				$game_id = preg_replace('/[^0-9]+/', '', $_POST['game_id']);
				chat::setCommentPref($game_id, $comment_id, 'comments_removed');
			}

			elseif($_GET['form'] == 'likeComment'){
				$comment_id = preg_replace('/[^0-9]+/', '', $_POST['id']);
				$game_id = preg_replace('/[^0-9]+/', '', $_POST['game_id']);
				chat::setCommentPref($game_id, $comment_id, 'comments_liked');
			}

			elseif($_GET['form'] == 'likeCommentRemove'){
				$comment_id = preg_replace('/[^0-9]+/', '', $_POST['id']);
				$game_id = preg_replace('/[^0-9]+/', '', $_POST['game_id']);
				chat::removeCommentLike($comment_id, $game_id);
			}

			elseif($_GET['form'] == 'followAct'){
				$uid = preg_replace('/[^0-9]+/', '', $_POST['uid']);
				profile::followAct($uid);
			}

			elseif($_GET['form'] == 'unfollowAct'){
				$uid = preg_replace('/[^0-9]+/', '', $_POST['uid']);
				profile::unfollowAct($uid);
			}

			elseif($_GET['form'] == 'submitGame'){
				$cat = preg_replace('/[^0-9]+/', '', $_POST['cat']);
				$embedUrl = urlencode(trim($_POST['embedURL']));
				$title = preg_replace('/[^A-Za-z0-9 ]+/', '', trim($_POST['title']));
				$description = preg_replace('/[^A-Za-z0-9., ]+/', '', trim($_POST['description']));
				$url = str_replace(' ', '_', $title);
				profile::submitGame($cat, $embedUrl, $title, $description, $url);
			}

			elseif($_GET['form'] == 'updateGame'){
				$id = preg_replace('/[^0-9]+/', '', $_POST['id']);
				$cat = preg_replace('/[^0-9]+/', '', $_POST['cat']);
				$embedUrl = urlencode(trim($_POST['embedURL']));
				$title = preg_replace('/[^A-Za-z0-9 ]+/', '', trim($_POST['title']));
				$description = preg_replace('/[^A-Za-z0-9., ]+/', '', trim($_POST['description']));
				$url = str_replace(' ', '_', $title);
				profile::updateGame($id, $cat, $embedUrl, $title, $description, $url);
			}

			elseif($_GET['form'] == 'updateLive'){
				$id = preg_replace('/[^0-9]+/', '', $_POST['id']);
				$cat = preg_replace('/[^0-9]+/', '', $_POST['cat']);
				$embedUrl = urlencode(trim($_POST['embedURL']));
				$title = preg_replace('/[^A-Za-z0-9 ]+/', '', trim($_POST['title']));
				$description = preg_replace('/[^A-Za-z0-9., ]+/', '', trim($_POST['description']));
				$url = str_replace(' ', '_', $title);
				profile::updateLive($id, $cat, $embedUrl, $title, $description, $url);
			}

			elseif($_GET['form'] == 'profileSearch'){
				$q = preg_replace('/[^A-Za-z0-9 ]+/', '', $_POST['q']);
				$handle = preg_replace('/[^A-Za-z0-9_]+/', '', $_POST['handle']);
				$data['games'] = profile::profileSearch($q, $handle);
				$data['page'] = 'profileSearch';
				view::build('profile'.DS.'profile', $data);
			}

			elseif($_GET['form'] == 'openResponder'){
				$comment_id = preg_replace('/[^0-9]+/', '', $_POST['comment_id']);
				$game_id = preg_replace('/[^0-9]+/', '', $_POST['game_id']);
				$comment = chat::fetchComments($game_id, $comment_id, 'parent');
				foreach($comment as $row){
					echo '
						<span id="message_viewer_close" onclick="closeMessageViewer()">X</span>
						<div class="mv_head">'.$row['author'].'</div>
						<div class="mv_timestamp">'.gmdate("d-m-y H:i:s", $row['timestamp']).'</div>
						<div class="comment_body_mv">'.$row['message'].'</div>
						<div class="comment_foot_m">
							
						</div>
						<textarea id="message_responder" placeholder="enter response"></textarea>
						<span id="btn-message_viewer_submit" onclick="sendResponse(this)">send</span>
					';
				}
			}

			elseif($_GET['form'] == 'fetchColour'){
				view::build('colourpicker');
			}

			elseif($_GET['form'] == 'saveBannerColour'){
				$colour = $_POST['colour'];
				profile::saveBannerColour($colour);
			}

			elseif($_GET['form'] == 'saveAvatar'){
				if(isset($_FILES['file']['name'])){
					profile::saveAvatar();
				}
			}

			elseif($_GET['form'] == 'bioSave'){
				profile::bioSave();
			}

			elseif($_GET['form'] == 'openView'){
				$comment_id = preg_replace('/[^0-9]+/', '', $_POST['comment_id']);
				$game_id = preg_replace('/[^0-9]+/', '', $_POST['game_id']);
				$comment = chat::fetchComments($game_id, $comment_id, 'parent');
				$responses = chat::fetchComments($game_id, $comment_id, 'responses');
				foreach($comment as $row){
					$back = ($row['par_id'] == 0)? '' : '<span class="icon-chevron-circle-left" onclick="openMessageViewer(this, \'view\', \''.$row['par_id'].'\');"></span>';
					$likeclass = (chat::fetchLikes($row['id'], $game_id) == 'false')? ' icon-thumbs-o-up' :' icon-thumbs-up';
					$likeClick = (chat::fetchLikes($row['id'], $game_id) == 'false')? 'commentLike(this, \''.$game_id.'\', \''.$comment_id.'\')' : 'removeLike(this, '.$game_id.', '.$comment_id.')';
					echo '
						<div data-id="'.$comment_id.'">
							<span id="message_viewer_close" onclick="closeMessageViewer()">X</span>
							<div class="mv_head">
								'.$back.'
								'.$row['author'].'
							</div>
							<div class="mv_timestamp">'.gmdate("d-m-y H:i:s", $row['timestamp']).'</div>
							<div class="comment_body_mv">'.$row['message'].'</div>
							<div class="comment_foot_m">
					';
					if(isset($_SESSION['auth']) && isset($_SESSION['uid'])){
						echo '
							<i class="icon-reply" onclick="openMessageViewer(this, \'respond\');"></i>
							<i class="icon-chat-like'.$likeclass.'" onclick="'.$likeClick.'"></i>
						';
					}
					
					echo '</div></div>';
					echo '<div class="response_bar">Responses</div>';
					echo '<div class="responses_block">';
					foreach($responses as $row){
						$likeclass = (chat::fetchLikes($row['id'], $game_id) == 'false')? ' icon-thumbs-o-up' :' icon-thumbs-up';
						$likeClick = (chat::fetchLikes($row['id'], $game_id) == 'false')? 'commentLike(this)' : 'removeLike(this, '.$game_id.', '.$row['id'].')';
						echo '
							<div class="response_sect" data-id="'.$row['id'].'">
								<div class="mv_head">
									'.$row['author'].'
								</div>
								<div class="mv_timestamp">'.gmdate("d-m-y H:i:s", $row['timestamp']).'</div>
								<div class="response_body">'.$row['message'].'</div>
								<div class="comment_foot_r">
							';
						if(isset($_SESSION['auth']) && isset($_SESSION['uid'])){
							echo '
								<i class="icon-reply" onclick="openMessageViewer(this, \'respond\');"></i>
								<i class="icon-chat-like'.$likeclass.'" onclick="'.$likeClick.'"></i>
								<i class="icon-eye" onclick="openMessageViewer(this, \'view\');"></i>
							';
						}else{
							echo '<i class="icon-eye eye-single" onclick="openMessageViewer(this, \'view\');"></i>';
						}

						echo '
							</div>
							</div>
						';
					}
					echo '<div>';
				}
			}

			elseif($_GET['form'] == 'commentRespond'){
				if(isset($_SESSION['auth'])){
					$key = str_replace(' ', '', $_SESSION['auth']);
					$uid = preg_replace('/[^0-9]+/', '', $_SESSION['uid']);
					if($key !== 0 && auth::sessionVerify($uid, $key) == 1){
						$userDetails = auth::fetchUser($uid);
						foreach($userDetails as $row){
							$author = $row['handle'];
						}
					}
					$parent_id = preg_replace('/[^0-9]+/', '', $_POST['parent_id']);
					$game_id = preg_replace('/[^0-9]+/', '', $_POST['game_id']);
					$message = char_convert_special($_POST['message']);
					chat::commentRespond($parent_id, $game_id, $message, $author);
				}
			}

			elseif($_GET['form'] == 'newComment'){
				$refurl = $_SERVER['HTTP_REFERER'];
				$id = explode("=", strstr($refurl, 'id='))[1];
				$dom = explode("/", substr($refurl, strpos($refurl, "/") + 1))[1];
				if(isset($_SESSION['auth'])){
					$key = str_replace(' ', '', $_SESSION['auth']);
					$uid = preg_replace('/[^0-9]+/', '', $_SESSION['uid']);
					if($key !== 0 && auth::sessionVerify($uid, $key) == 1){
						$userDetails = auth::fetchUser($uid);
						foreach($userDetails as $row){
							$author = $row['handle'];
						}
					}
					$timestamp = time();
					$game_id = preg_replace('/[^0-9]+/', '', $_POST['game_id']);
					$message = char_convert_special($_POST['message']);
					chat::newComment($game_id, $message, $timestamp, $author);
					echo '
						<i class="comment_ell_menu icon-ellipsis-h"></i>
						<div class="comment_head">'.$author.'</div>
						<div class="comment_body">'.$message.'</div>
						<div class="comment_foot_m">
							<i class="icon-reply"></i>
							<i class="icon-chat-like icon-thumbs-o-up"></i>
							<i class="icon-eye"></i>
							<div class="comment_timestamp">'.gmdate("d-m-y H:i:s", $timestamp).'</div>
						</div>
					';
				}
			}
		}
	}
	
	public static function cookie_set($type, $id){
		$cookie_name = ($type == 'like')? 'liked' : (($type == 'dislike')? 'disliked' : 'fav');
		if(!isset($_COOKIE[$cookie_name])){
			setcookie($cookie_name, $id.',', time() + (86400 * 90), "/");
		}else{
			$liked_array = explode(',', $_COOKIE[$cookie_name]);
			if(!in_array($id, $liked_array)){
				$cookie_value = $_COOKIE[$cookie_name].$id.',';
				setcookie($cookie_name, $cookie_value, time() + (86400 * 90), "/");
			}
		}
	}
	
	public static function undo_like($type, $id){
		
	}
}


