<?php
include_model('auth');
include_model('profile');
include_controller('app');
use profileModel as profile;
use authModel as auth;
use appController as app;
class profileController {

	public static function index(){
		$metadata['meta']['title'] = SITENAME;
		$metadata['meta']['description'] = SITENAME;
		$footdata['copyright'] = '&#169; '.date('Y').' '.SITENAME;
		

		if(!isset($_GET['handle'])){
			if(isset($_POST['action'])){
				if($_POST['action'] == 'gameAdmin'){
					$game_id = preg_replace('/[^0-9]+/', '', $_POST['game_id']);
					$data['game_id'] = $game_id;
					$page = preg_replace('/[^a-z]+/', '', $_POST['page']);
					$data['page'] = $page;

					if($page == 'general'){
						$gameInfo = profile::fetchGameInfo($game_id);
						foreach($gameInfo as $row){
							$data['game_title'] = $row['title'];
							$data['game_description'] = $row['description'];
						}
					}

					elseif($page == 'stats'){
						$statsInfo = profile::fetchGameStats($game_id);
						foreach($statsInfo as $row){
							$data['imp'] = number_format($row['imp']);
							$data['hits'] = number_format($row['hits']);
							$data['likes'] = number_format($row['likes']);
							$data['downloads'] = number_format($row['downloads']);
						}
					}

					elseif($page == 'update'){
						$statsInfo = profile::fetchGameStats($game_id);
					}

					elseif($page == 'psmoGameNew'){
						
					}

					view::build('profile'.DS.'gameMenu', $data);
				}elseif($_POST['action'] == 'liveRequest'){
					$game_id = preg_replace('/[^0-9]+/', '', $_POST['game_id']);
					profile::liveRequest($game_id);
				}
			}else{
				$bannerColour = profile::fetchBannerColour();
				if($bannerColour){
					foreach($bannerColour as $bcrow){
						$data['bannerColour'] = $bcrow['bannerColour'];
					}
				}
				
				if(isset($_SESSION['auth'])){
					header('Expires: '.date("l, d m Y h:i:s").' GMT');
					header('Cache-Control: no-store, no-cache, must-revalidate');
					header('Cache-Control: post-check=0, pre-check=0', FALSE);
					header('Pragma: no-cache');
					$key = str_replace(' ', '', $_SESSION['auth']);
					$uid = preg_replace('/[^0-9]+/', '', $_SESSION['uid']);

					if(auth::sessionVerify($uid, $key) == 1){
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
							
							if(isset($_POST['page'])){
								$page = preg_replace('/[^a-zA-Z]+/', '', $_POST['page']);
								$data['page'] = $page;
								if($page == 'following'){
									$data['following'] = profile::fetchFollowing($handle);
								}elseif($page == 'settings'){
									$settings = profile::fetchSettings($handle);
									foreach($settings as $row){
										$data[$row['key']] = $row['val'];
									}
								}elseif($page == 'psmoGameNew'){
									$data['page'] = 'psmoGameNew';
									$data['cat'] = profile::fetchCats();
								}elseif($page == 'psmoSandbox'){
									$data['page'] = 'psmoSandbox';
									$data['games'] = profile::fetchFromSandbox();
								}elseif($page == 'liked'){
									$data['likes'] = profile::fetchLikes($handle);
								}elseif($page == 'games'){
									$data['games'] = profile::fetchGames($handle);
								}elseif($page == 'live'){
									$data['page'] = 'live';
									$game_id = preg_replace('/[^0-9]+/', '', $_POST['game_id']);
									$data['game'] = profile::fetchGameInfo($game_id);
									$data['cat'] = profile::fetchCats();
								}
							}
							elseif (isset($_GET['page'])) {
								$page = preg_replace('/[^a-zA-Z]+/', '', $_GET['page']);
								$data['page'] = $page;
								if($page == 'sandbox'){
									if(isset($_GET['action'])){
										$action = preg_replace('/[^a-z]+/', '', $_GET['action']);
										if(isset($_GET['game_id'])){
											$game_id = preg_replace('/[^0-9]+/', '', $_GET['game_id']);
											$data['game'] = profile::fetchSanboxGame($game_id);
											$data['cat'] = profile::fetchCats();
											$data['game_id'] = $game_id;
										}else{
											view::build('error');
											die();
										}
									}else{
										view::build('error');
										die();
									}
									
								}
							}
							else{
								$data['page'] = 'liked';
								$data['likes'] = profile::fetchLikes($handle);
								profileController::liked($img, $username, $handle);
							}
						}else{
							$data['page'] = 'nolog';
							if(isset($_POST['request'])){
								view::build('profile'.DS.'profile', $data);
							}else{
								view::build('head', $metadata).
								view::build('profile'.DS.'profile', $data).
								view::build('foot', $footdata);
							}
						}
						if(isset($_POST['request'])){
							if($_POST['request'] == 'origin'){
								view::build('profile'.DS.'profileHead', $data).
								view::build('profile'.DS.'profile', $data).
								view::build('profile'.DS.'profileFoot');
							}else{
								view::build('profile'.DS.'profile', $data);
							}
						}else{
							view::build('head', $metadata).
							view::build('profile'.DS.'profileHead', $data).
							view::build('profile'.DS.'profile', $data).
							view::build('profile'.DS.'profileFoot').
							view::build('foot', $footdata);
						}
					}else{
						$data['page'] = 'nolog';
						if(isset($_POST['request'])){
							view::build('profile'.DS.'profile', $data);
						}else{
							view::build('head', $metadata).
							view::build('profile'.DS.'profile', $data).
							view::build('foot', $footdata);
						}
					}
			}
		}

		else{
			$data['page'] = 'profileFetch';
			$h = preg_replace('/[^a-zA-z0-9_]+/', '', $_GET['handle']);
			if(isset($_SESSION['auth']) && isset($_SESSION['uid'])){
				$key = str_replace(' ', '', $_SESSION['auth']);
				$uid = preg_replace('/[^0-9]+/', '', $_SESSION['uid']);
				if($key !== 0 && auth::sessionVerify($uid, $key) == 1){
					$userDetails = auth::fetchUser($uid);
					foreach($userDetails as $row){
						$handle = $row['handle'];
					}
					$ufl = 0;
					$img = '/assets/images/users/'.$handle[0].'/'.$handle[1].'/'.$handle[2].'/'.$handle.'/';
					$metadata['meta']['img'] = $img;
					$profileId = profile::userId($h);
					foreach($profileId as $row){
						$pid = $row['uid'];
					}
					$following = profile::fetchFollowing($handle);
					if($following){
						foreach ($following as $row) {
							if($row['uid'] == $pid){
								$ufl = 1;
							}
						}
					}
					$data['followBtn'] = ($ufl == 0)? '<span id="btnFollow" class="btnm btnProfileFollow" data-id="'.$pid.'">follow</span></div><div class="pcbu"><div class="profileFollowBlock">' : '<span id="btnUnfollow" class="btnm btnProfileFollow" data-id="'.$pid.'">following</span></div><div class="pcbu"><div class="profileFollowBlock">';
				}
			}else{
				$data['followBtn'] = '';
			}

			$img = '/assets/images/users/'.$h[0].'/'.$h[1].'/'.$h[2].'/'.$h.'/';
			$data['img'] = $img;
			$data['handle'] = $h;

			$uname = profile::userProfile($h);
			foreach ($uname as $row) {
				$username = $row['username'];
			}
			$data['username'] = $username;

			$uid = profile::userId($h);
			foreach ($uid as $row) {
				$uid = $row['uid'];
			}
			$data['uid'] = $uid;

			$bannerColour = profile::fetchBannerColour($h);
			foreach($bannerColour as $bcrow){
				$data['bannerColour'] = $bcrow['bannerColour'];
			}

			$data['games'] = profile::fetchGames($h);

			if(isset($_POST['request'])){
				view::build('profile'.DS.'profile', $data);
			}else{
				view::build('head', $metadata).
				view::build('profile'.DS.'profileHead', $data).
				view::build('profile'.DS.'profile', $data).
				view::build('foot', $footdata);
			}
		}
	}
	
	public static function liked($img, $uname, $handle){
		$metadata['meta']['title'] = SITENAME;
		$metadata['meta']['description'] = SITENAME;
		$metadata['meta']['img'] = $img;
		$data['page'] = 'liked';
		$data['img'] = $img;
		$data['username'] = $uname;
		$data['handle'] = $handle;
		$data['likes'] = profile::fetchLikes($handle);
		$bannerColour = profile::fetchBannerColour();
			foreach($bannerColour as $bcrow){
				$data['bannerColour'] = $bcrow['bannerColour'];
			}
		$footdata['copyright'] = '&#169; '.date('Y').' '.SITENAME;
		
		if(isset($_POST['request'])){
			if($_POST['request'] == 'origin'){
				view::build('profile'.DS.'profileHead', $data).
				view::build('profile'.DS.'profile', $data).
				view::build('profile'.DS.'profileFoot');
			}else{
				view::build('profile'.DS.'profile', $data);
			}
		}else{
			view::build('head', $metadata).
			view::build('profile'.DS.'profileHead', $data).
			view::build('profile'.DS.'profile', $data).
			view::build('profile'.DS.'profileFoot').
			view::build('foot', $footdata);
		}
	}
}


