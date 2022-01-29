<?php
include_model('auth');
use authModel as auth;
class authController {

	public static function index(){
		if(isset($_GET['action'])){
			$action = $_GET['action'];
			if($action == 'cookie_accept'){
				$type = preg_replace('/[^a-z_]+/', '', $_POST['type']);
				$id = preg_replace('/[^0-9]+/', '', $_POST['id']);
				formController::cookie_set($type, $id);

				if($type != 'fav'){
					$undo_type = ($type == 'like')? 'dislike' : 'like';
					formController::undo_like($undo_type, $id);
				}
				die();
			}

			elseif($action == 'authSubmit'){
				$name = preg_replace('/[^a-zA-Z0-9 ]+/', '', $_POST['name']);
				$username = preg_replace('/[^a-zA-Z0-9_]+/', '', $_POST['username']);
				$password = str_replace(' ', '', $_POST['pass']);

				if($_POST['type'] == 'passRec'){
					echo auth::recConfirm($username, $password);
				}

				elseif($_POST['type'] == 'login'){
					if(auth::confirm('username', $username) == 1){
						echo auth::authSubmit($name, $username, $password);					
					}else{
						echo 0;
					}
				}
				
				else{
					if(auth::confirm('username', $username) == 0){
						echo auth::registerSubmit($name, $username, $password);
					}else{
						echo 0;
					}
				}
			}

			elseif($action == 'downloadKey'){
				echo $_SESSION['key'];
				unset($_SESSION['key']);
			}

			elseif($action == 'changePassword'){
				$username = preg_replace('/[^a-zA-Z0-9_]+/', '', $_POST['username']);
				$password = str_replace(' ', '', $_POST['pass']);
				$key = str_replace(' ', '', $_POST['key']);
				auth::changePassword($username, $key, $password);
			}

			elseif($action == 'checkSession'){
				echo (isset($_SESSION['auth']) && isset($_SESSION['uid']))? 1 : 0;
			}

			elseif($action == 'logout'){
				unset($_SESSION['auth']);
				unset($_SESSION['id']);
			}
		}else{
				$userDetails = fetchUserDetails();
				if(isset($userDetails[0]['handle'])){
					$data['handle'] = $userDetails[0]['handle'];
					$img = $userDetails[0]['img'];
					$metadata['meta']['img'] = $img;
				}
				$metadata['meta']['title'] = SITENAME.' - page not found';
				$metadata['meta']['description'] = '';
					
				$data['content'] = 'Page not found';

				$data['copyright'] = '&#169; '.date('Y').' '.SITENAME;
				view::build('head', $metadata).
			view::build('error', $data);
		}
	}
}


