<?php
include_model('auth');
use authModel as auth;
class profileModel {
	public static function fetchGameInfo($game_id){
		$data = array();
		$db = new SQLite3(SYS.'db'.DS.'games.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT id, title, description, url, embed, cat
			FROM info
			WHERE id = :game_id
		';

		if($query = $db->prepare($query)){
			$query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
			$result = $query->execute();

			while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
				$data[] = array(   
					'game_id' => $row['id'],
					'title' => $row['title'],
					'description' => $row['description'],
					'url' => $row['url'],
					'embed' => $row['embed'],
					'cat' => $row['cat']
				);
			}

			$result->finalize();
			$query->close();
		}

		$db->close();

		return $data;
	}

	public static function fetchCats(){
		$data = array();
		$db = new SQLite3(SYS.'db'.DS.'games.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT id, key
			FROM cats
		';

		if($query = $db->prepare($query)){
			$result = $query->execute();

			while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
				$data[] = array(   
					'id' => $row['id'],
					'key' => $row['key']
				);
			}

			$result->finalize();
			$query->close();
		}

		$db->close();

		return $data;
	}

	public static function profileSearch($q, $h){
		$data = array();
		$db = new SQLite3(SYS.'db'.DS.'users.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT handle
			FROM ud
			WHERE handle = :handle
			LIMIT 1
		';
		if($query = $db->prepare($query)){
			$query->bindValue(':handle', $h, SQLITE3_TEXT);
			$result = $query->execute();

			if($row = $result->fetchArray(SQLITE3_ASSOC) ) {
				$q = '%'.$q.'%';
				$dba = new SQLite3(SYS.'db'.DS.'users'.DS.$h[0].DS.$h[1].DS.$h[2].DS.$h.'.db', SQLITE3_OPEN_READONLY);
				$querya = '
					SELECT id, title, embed, url, cat
					FROM games_info
					WHERE title LIKE :q
				';
				if($querya = $dba->prepare($querya)){
					$querya->bindValue(':q', $q, SQLITE3_TEXT);
					$resulta = $querya->execute();

					while($rowa = $resulta->fetchArray(SQLITE3_ASSOC) ) {
						$data[] = array(   
							'id' => $rowa['id'],
							'title' => $rowa['title'],
							'url' => $rowa['url'],
							'embed' => $rowa['embed'],
							'cat' => $rowa['cat']
						);
					}				

					$resulta->finalize();
					$querya->close();
				}

				$dba->close();

				$result->finalize();
				$query->close();
			}else{
				
			}
		}

		$db->close();

		return $data;
	}

	public static function liveRequest($id){
		$return = 0;
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

				$db = new SQLite3(SYS.'db'.DS.'main.db', SQLITE3_OPEN_READWRITE);
				$query = '
					SELECT game_id
					FROM liverequests
					WHERE game_id = :game_id
					AND username = :handle
					LIMIT 1
				';
				if($query = $db->prepare($query)){
					$query->bindValue(':game_id', $id, SQLITE3_INTEGER);
					$query->bindValue(':handle', $handle, SQLITE3_TEXT);
					$result = $query->execute();

					while ($row = $result->fetchArray(SQLITE3_ASSOC) ) { 
							$return = 1;
					}

					$result->finalize();
					$query->close();
				}

				if($return == 0){
					$query = 'INSERT INTO "liverequests" ("game_id", "username") VALUES (:game_id, :username)';
					if($query = $db->prepare($query)){
						$query->bindValue(':game_id', $id, SQLITE3_INTEGER);
						$query->bindValue(':username', $handle, SQLITE3_TEXT);
						$query->execute();
						$query->close();
					}

					$db->close();

					$db = new SQLite3(SYS.'db'.DS.'users'.DS.$f1.DS.$f2.DS.$f3.DS.$handle.'.db', SQLITE3_OPEN_READWRITE);
					$query = 'UPDATE "sandbox" SET req = "1" WHERE id = :id';
					if($query = $db->prepare($query)){
						$query->bindValue(':id', $id, SQLITE3_INTEGER);
						$query->execute();
						$query->close();
					}
					$db->close();
				}
			}
		}
	}

	public static function bioSave(){
		$return = 0;
		$bioContent = char_convert_special($_POST['bioContent']);
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

				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$f1.DS.$f2.DS.$f3.DS.$handle.'.db', SQLITE3_OPEN_READWRITE);
				$query = 'UPDATE "profile" SET value = :bioContent WHERE key = "profile_content"';
				if($query = $db->prepare($query)){
					$query->bindValue(':bioContent', $bioContent, SQLITE3_TEXT);
					$query->execute();
					$query->close();
					$return = 1;
				}
				$db->close();
			}
		}
		echo $return;
	}

	public static function saveBannerColour($colour){
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

				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$f1.DS.$f2.DS.$f3.DS.$handle.'.db', SQLITE3_OPEN_READWRITE);
				$query = 'UPDATE "profile" SET value = :colour WHERE key = "banner"';
				if($query = $db->prepare($query)){
					$query->bindValue(':colour', $colour, SQLITE3_TEXT);
					$query->execute();
					$query->close();
				}
				$db->close();
			}
		}
	}

	public static function fetchBannerColour($h=''){
		if($h == ''){
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

					$data = array();
					$db = new SQLite3(SYS.'db'.DS.'users'.DS.$f1.DS.$f2.DS.$f3.DS.$handle.'.db', SQLITE3_OPEN_READONLY);
					$query = '
						SELECT value
						FROM profile
						WHERE key = "banner"
					';
					if($query = $db->prepare($query)){
						$result = $query->execute();

						while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
							$data[] = array(   
								'bannerColour' => $row['value']
							);
						}

						$result->finalize();
						$query->close();
					}

					$db->close();

					return $data;
				}
			}
		}else{
			$data = array();
			$db = new SQLite3(SYS.'db'.DS.'users'.DS.$h[0].DS.$h[1].DS.$h[2].DS.$h.'.db', SQLITE3_OPEN_READONLY);
			$query = '
				SELECT value
				FROM profile
				WHERE key = "banner"
			';
			if($query = $db->prepare($query)){
				$result = $query->execute();

				while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
					$data[] = array(   
						'bannerColour' => $row['value']
					);
				}

				$result->finalize();
				$query->close();
			}

			$db->close();
			return $data;
		}
	}

	public static function saveAvatar(){
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

				$filename = $_FILES['file']['name'];

				$location = 'assets/images/users/'.$f1.'/'.$f2.'/'.$f3.'/'.$handle.'/avatar.png';
				$imageFileType = pathinfo($location,PATHINFO_EXTENSION);
				$imageFileType = strtolower($imageFileType);

				$valid_extensions = array('png');
				
				if(in_array(strtolower($imageFileType), $valid_extensions)) {
					move_uploaded_file($_FILES['file']['tmp_name'],$location);
				}
			}
		}
	}

	public static function fetchGameStats($game_id){
		$game_id = preg_replace('/[^0-9]+/', '', $game_id);
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
				$img = '/assets/images/users/'.$f1.'/'.$f2.'/'.$f3.'/'.$userlow.'/';

				$data = array();
				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$f1.DS.$f2.DS.$f3.DS.$userlow.'.db', SQLITE3_OPEN_READONLY);
				$query = '
					SELECT game_id, imp, hits, likes, downloads
					FROM games_stats
					WHERE game_id = :game_id
				';

				if($query = $db->prepare($query)){
					$query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
					$result = $query->execute();

					while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
						$data[] = array(   
							'game_id' => $row['game_id'],
							'imp' => $row['imp'],
							'hits' => $row['hits'],
							'likes' => $row['likes'],
							'downloads' => $row['downloads']
						);
					}

					$result->finalize();
					$query->close();
				}

				$db->close();

				return $data;
			}
		}
	}



	public static function userProfile($handle){
		$data = array();
		$f1 = $handle[0];
		$f2 = $handle[1];
		$f3 = $handle[2];
		$db = new SQLite3(SYS.'db'.DS.'users'.DS.$f1.DS.$f2.DS.$f3.DS.$handle.'.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT value
			FROM profile
			WHERE key = "username"
			LIMIT 1
		';

		if($query = $db->prepare($query)){
			$result = $query->execute();

			while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
				$data[] = array(
					'username' => $row['value']
				);
			}
			$result->finalize();
			$query->close();
		}

		$db->close();

		return $data;
	}

	public static function userId($handle){
		$data = array();
		$db = new SQLite3(SYS.'db'.DS.'users.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT uid
			FROM ud
			WHERE handle = "'.$handle.'"
			LIMIT 1
		';

		if($query = $db->prepare($query)){
			$result = $query->execute();

			while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
				$data[] = array(
					'uid' => $row['uid']
				);
			}
			$result->finalize();
			$query->close();
		}

		$db->close();

		return $data;
	}

	public static function fetchLikes($usr){
		$data = array();
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
				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$usr[0].DS.$usr[1].DS.$usr[2].DS.$usr.'.db', SQLITE3_OPEN_READONLY);
				$query = '
					SELECT game_id
					FROM games_liked
				';

				if($query = $db->prepare($query)){
					$result = $query->execute();

					while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
						$data[] = array(   
							'game_id' => $row['game_id']
						);
					}

					$result->finalize();
					$query->close();
				}

				$db->close();
			}
		}

		return $data;
	}

	public static function fetchUser($usr){
		$data = array();
		$db = new SQLite3(SYS.'db'.DS.'users'.DS.$usr[0].DS.$usr[1].DS.$usr[2].DS.$usr.'.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT game_id
			FROM games_liked
		';

		if($query = $db->prepare($query)){
			$result = $query->execute();

			while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
				$data[] = array(   
					'game_id' => $row['game_id']
				);
			}

			$result->finalize();
			$query->close();
		}

		$db->close();

		return $data;
	}

	public static function likeCheck($usr, $game_id){
		$data = 0;
		$db = new SQLite3(SYS.'db'.DS.'users'.DS.$usr[0].DS.$usr[1].DS.$usr[2].DS.$usr.'.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT game_id
			FROM games_liked
			WHERE game_id = :game_id
		';

		if($query = $db->prepare($query)){
			$query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
			$result = $query->execute();

			while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
				$data = 1;
			}

			$result->finalize();
			$query->close();
		}

		$db->close();

		return $data;
	}

	public static function setLike($usr, $game_id){
		$db = new SQLite3(SYS.'db'.DS.'users'.DS.$usr[0].DS.$usr[1].DS.$usr[2].DS.$usr.'.db', SQLITE3_OPEN_READWRITE);
		$query = 'INSERT INTO "games_liked" ("game_id") VALUES (:game_id)';
		if($query = $db->prepare($query)){
			$query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
			$query->execute();
			$query->close();
		}
		$db->close();
	}

	public static function unsetLike($usr, $game_id){
		$db = new SQLite3(SYS.'db'.DS.'users'.DS.$usr[0].DS.$usr[1].DS.$usr[2].DS.$usr.'.db', SQLITE3_OPEN_READWRITE);
		$query = 'DELETE FROM "games_liked" WHERE game_id = :game_id';
		if($query = $db->prepare($query)){
			$query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
			$query->execute();
			$query->close();
		}
		$db->close();
	}

	public static function followAct($fid){
		if(isset($_SESSION['auth']) && isset($_SESSION['uid'])){
			$key = str_replace(' ', '', $_SESSION['auth']);
			$uid = preg_replace('/[^0-9]+/', '', $_SESSION['uid']);
			if($key !== 0 && auth::sessionVerify($uid, $key) == 1){
				$userDetails = auth::fetchUser($uid);
				foreach($userDetails as $row){
					$handle = $row['handle'];
				}
				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$handle[0].DS.$handle[1].DS.$handle[2].DS.$handle.'.db', SQLITE3_OPEN_READWRITE);
				$query = 'INSERT INTO "following" ("uid") VALUES (:uid)';
				if($query = $db->prepare($query)){
					$query->bindValue(':uid', $fid, SQLITE3_INTEGER);
					$query->execute();
					$query->close();
				}

				$db->close();
			}
		}
	}

	public static function unfollowAct($fid){
		if(isset($_SESSION['auth']) && isset($_SESSION['uid'])){
			$key = str_replace(' ', '', $_SESSION['auth']);
			$uid = preg_replace('/[^0-9]+/', '', $_SESSION['uid']);
			if($key !== 0 && auth::sessionVerify($uid, $key) == 1){
				$userDetails = auth::fetchUser($uid);
				foreach($userDetails as $row){
					$handle = $row['handle'];
				}
				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$handle[0].DS.$handle[1].DS.$handle[2].DS.$handle.'.db', SQLITE3_OPEN_READWRITE);
				$query = 'DELETE FROM "following" WHERE uid = :uid';
				if($query = $db->prepare($query)){
					$query->bindValue(':uid', $fid, SQLITE3_INTEGER);
					$query->execute();
					$query->close();
				}

				$db->close();
			}
		}
	}

	public static function fetchFollowing($usr){
		$data = array();
		$db = new SQLite3(SYS.'db'.DS.'users'.DS.$usr[0].DS.$usr[1].DS.$usr[2].DS.$usr.'.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT uid
			FROM following
		';

		if($query = $db->prepare($query)){
			$result = $query->execute();

			while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
				$data[] = array(   
					'uid' => $row['uid']
				);
			}

			$result->finalize();
			$query->close();
		}

		$db->close();

		if($data){
			$db = new SQLite3(SYS.'db'.DS.'users.db', SQLITE3_OPEN_READONLY);

			$i = 1;
			$where = '';
			$handle_data = array();
			foreach($data as $row){
				$uid = preg_replace('/[^0-9]+/', '', $row['uid']);
				$where .= ($i == 1)? 'WHERE uid = '.$uid : ' OR uid = '.$uid;
				$i++;
			}

			$query = '
				SELECT uid, handle
				FROM ud
				'.$where.'
			';

			if($query = $db->prepare($query)){
				$result = $query->execute();

				while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
					$handle_data[] = array(   
						'uid' => $row['uid'],
						'handle' => $row['handle']
					);
				}

				$result->finalize();
				$query->close();
			}

			$db->close();

			return $handle_data;
		}
	}

	public static function fetchSettings($usr){
		$data = array();
		$db = new SQLite3(SYS.'db'.DS.'users'.DS.$usr[0].DS.$usr[1].DS.$usr[2].DS.$usr.'.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT key, value
			FROM profile
		';

		if($query = $db->prepare($query)){
			$result = $query->execute();

			while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
				$data[] = array(   
					'key' => $row['key'],
					'val' => $row['value']
				);
			}

			$result->finalize();
			$query->close();
		}

		$db->close();

		return $data;
	}

	public static function fetchGames($h){
		$data = array();
		$db = new SQLite3(SYS.'db'.DS.'users'.DS.$h[0].DS.$h[1].DS.$h[2].DS.$h.'.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT id, title, url, embed
			FROM games_info
		';

		if($query = $db->prepare($query)){
			$result = $query->execute();

			while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
				$data[] = array(   
					'id' => $row['id'],
					'title' => $row['title'],
					'url' => $row['url'],
					'embed' => $row['embed']
				);
			}

			$result->finalize();
			$query->close();
		}

		$db->close();

		return $data;
	}

	public static function fetchFromSandbox(){
		if(isset($_SESSION['auth']) && isset($_SESSION['uid'])){
			$key = str_replace(' ', '', $_SESSION['auth']);
			$uid = preg_replace('/[^0-9]+/', '', $_SESSION['uid']);
			if($key !== 0 && auth::sessionVerify($uid, $key) == 1){
				$userDetails = auth::fetchUser($uid);
				foreach($userDetails as $row){
					$handle = $row['handle'];
				}
				$data = array();
				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$handle[0].DS.$handle[1].DS.$handle[2].DS.$handle.'.db', SQLITE3_OPEN_READONLY);
				$query = '
					SELECT id, title, url, embed
					FROM sandbox
				';

				if($query = $db->prepare($query)){
					$result = $query->execute();

					while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
						$data[] = array(   
							'id' => $row['id'],
							'title' => $row['title'],
							'url' => $row['url'],
							'embed' => $row['embed']
						);
					}

					$result->finalize();
					$query->close();
				}

				$db->close();

				return $data;
			}
		}
	}

	public static function fetchSanboxGame($game_id){
		if(isset($_SESSION['auth']) && isset($_SESSION['uid'])){
			$key = str_replace(' ', '', $_SESSION['auth']);
			$uid = preg_replace('/[^0-9]+/', '', $_SESSION['uid']);
			$game_id = preg_replace('/[^0-9]+/', '', $game_id);
			if($key !== 0 && auth::sessionVerify($uid, $key) == 1){
				$userDetails = auth::fetchUser($uid);
				foreach($userDetails as $row){
					$handle = $row['handle'];
				}
				$data = array();
				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$handle[0].DS.$handle[1].DS.$handle[2].DS.$handle.'.db', SQLITE3_OPEN_READONLY);
				$query = '
					SELECT id, title, description, url, embed, cat, req
					FROM sandbox
					WHERE "id" = :game_id
				';

				if($query = $db->prepare($query)){
					$query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
					$result = $query->execute();

					while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
						$data[] = array(   
							'id' => $row['id'],
							'title' => $row['title'],
							'description' => $row['description'],
							'url' => $row['url'],
							'embed' => $row['embed'],
							'cat' => $row['cat'],
							'req' => $row['req']
						);
					}

					$result->finalize();
					$query->close();
				}

				$db->close();

				return $data;
			}
		}
	}

	public static function updateLive($id, $cat, $embed, $title, $description, $url){
		if(isset($_SESSION['auth']) && isset($_SESSION['uid'])){
			$key = str_replace(' ', '', $_SESSION['auth']);
			$uid = preg_replace('/[^0-9]+/', '', $_SESSION['uid']);
			$game_id = preg_replace('/[^0-9]+/', '', $id);
			if($key !== 0 && auth::sessionVerify($uid, $key) == 1){
				$userDetails = auth::fetchUser($uid);
				foreach($userDetails as $row){
					$handle = $row['handle'];
				}

				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$handle[0].DS.$handle[1].DS.$handle[2].DS.$handle.'.db', SQLITE3_OPEN_READONLY);
				$q = '
					SELECT id
					FROM games_info
					WHERE id = :id
					LIMIT 1
				';

				$r = 0;
				if($q = $db->prepare($q)){
					$q->bindValue(':id', $id, SQLITE3_INTEGER);
					$result = $q->execute();

					if ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
						$r = 1;
					}
				}
				$q->close();
				$db->close();

				if($r == 1){
					$db = new SQLite3(SYS.'db'.DS.'users'.DS.$handle[0].DS.$handle[1].DS.$handle[2].DS.$handle.'.db', SQLITE3_OPEN_READWRITE);
					$query = 'UPDATE "games_info" SET title = :title, description = :description, url = :url, embed = :embed, cat = :cat WHERE id = :id';
					if($query = $db->prepare($query)){
						$query->bindValue(':id', $id, SQLITE3_INTEGER);
						$query->bindValue(':title', $title, SQLITE3_TEXT);
						$query->bindValue(':description', $description, SQLITE3_TEXT);
						$query->bindValue(':url', $url, SQLITE3_TEXT);
						$query->bindValue(':embed', $embed, SQLITE3_TEXT);
						$query->bindValue(':cat', $cat, SQLITE3_INTEGER);
						$query->execute();
						$query->close();
					}
					$db->close();

					$db = new SQLite3(SYS.'db'.DS.'games.db', SQLITE3_OPEN_READWRITE);
					$query = 'UPDATE "info" SET title = :title, description = :description, url = :url, embed = :embed, cat = :cat WHERE id = :id';
					if($query = $db->prepare($query)){
						$query->bindValue(':id', $id, SQLITE3_INTEGER);
						$query->bindValue(':title', $title, SQLITE3_TEXT);
						$query->bindValue(':description', $description, SQLITE3_TEXT);
						$query->bindValue(':url', $url, SQLITE3_TEXT);
						$query->bindValue(':embed', $embed, SQLITE3_TEXT);
						$query->bindValue(':cat', $cat, SQLITE3_INTEGER);
						$query->execute();
						$query->close();
					}
					$db->close();

					if($_FILES){
						$filename = $_FILES['file']['name'];
						$imageFileType = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
						$valid_extensions = array('png');
						$loc = 'assets'.DS.'images'.DS.'games'.DS.$id.DS.'thumb.png';
							
						if(in_array($imageFileType, $valid_extensions)) {
							move_uploaded_file($_FILES['file']['tmp_name'], $loc);
						}
					}
				}
			}
		}
	}

	public static function updateGame($id, $cat, $embed, $title, $description, $url){
		if(isset($_SESSION['auth']) && isset($_SESSION['uid'])){
			$key = str_replace(' ', '', $_SESSION['auth']);
			$uid = preg_replace('/[^0-9]+/', '', $_SESSION['uid']);
			$game_id = preg_replace('/[^0-9]+/', '', $id);
			if($key !== 0 && auth::sessionVerify($uid, $key) == 1){
				$userDetails = auth::fetchUser($uid);
				foreach($userDetails as $row){
					$handle = $row['handle'];
				}
				$data = array();
				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$handle[0].DS.$handle[1].DS.$handle[2].DS.$handle.'.db', SQLITE3_OPEN_READWRITE);
				$query = 'UPDATE "sandbox" SET title = :title, description = :description, url = :url, embed = :embed, cat = :cat WHERE id = :id';
				if($query = $db->prepare($query)){
					$query->bindValue(':id', $id, SQLITE3_INTEGER);
					$query->bindValue(':title', $title, SQLITE3_TEXT);
					$query->bindValue(':description', $description, SQLITE3_TEXT);
					$query->bindValue(':url', $url, SQLITE3_TEXT);
					$query->bindValue(':embed', $embed, SQLITE3_TEXT);
					$query->bindValue(':cat', $cat, SQLITE3_INTEGER);
					$query->execute();
					$query->close();
				}
				$db->close();

				if($_FILES){
					$filename = $_FILES['file']['name'];
					$imageFileType = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
					$valid_extensions = array('png');
					$loc = 'assets'.DS.'images'.DS.'users'.DS.$handle[0].DS.$handle[1].DS.$handle[2].DS.$handle.DS.'sandbox'.DS.$id.'/thumb.png';
					
					if(in_array($imageFileType, $valid_extensions)) {
						move_uploaded_file($_FILES['file']['tmp_name'], $loc);
					}
				}else{
					echo 'no files';
				}
			}
		}
	}

	public static function submitGame($cat, $embed, $title, $description, $url){
		if(isset($_SESSION['auth']) && isset($_SESSION['uid'])){
			$key = str_replace(' ', '', $_SESSION['auth']);
			$uid = preg_replace('/[^0-9]+/', '', $_SESSION['uid']);
			if($key !== 0 && auth::sessionVerify($uid, $key) == 1){
			//	$db = new SQLite3(SYS.'db'.DS.'games.db', SQLITE3_OPEN_READWRITE);
			/*	$query = '
					INSERT INTO info
					("title", "description", "url", "embed", "cat")
					VALUES
					(:title, :description, :url, :embed, :cat)';
				if($query = $db->prepare($query)){
					$query->bindValue(':title', $title, SQLITE3_TEXT);
					$query->bindValue(':description', $description, SQLITE3_TEXT);
					$query->bindValue(':url', $url, SQLITE3_TEXT);
					$query->bindValue(':embed', $embed, SQLITE3_TEXT);
					$query->bindValue(':cat', $cat, SQLITE3_INTEGER);
					$query->execute();
					$query->close();
				}
				
				$db->close();
				*/

				$userDetails = auth::fetchUser($uid);
				foreach($userDetails as $row){
					$handle = $row['handle'];
				}
				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$handle[0].DS.$handle[1].DS.$handle[2].DS.$handle.'.db', SQLITE3_OPEN_READWRITE);
				$query = '
					INSERT INTO sandbox
					("title", "description", "url", "embed", "cat")
					VALUES
					(:title, :description, :url, :embed, :cat)';
				if($query = $db->prepare($query)){
					$query->bindValue(':title', $title, SQLITE3_TEXT);
					$query->bindValue(':description', $description, SQLITE3_TEXT);
					$query->bindValue(':url', $url, SQLITE3_TEXT);
					$query->bindValue(':embed', $embed, SQLITE3_TEXT);
					$query->bindValue(':cat', $cat, SQLITE3_INTEGER);
					$query->execute();
					$query->close();
				}
				$query = 'SELECT last_insert_rowid() as id';
				if($query = $db->prepare($query)){
					$result = $query->execute();

					while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
						$id = $row['id'];
					}

					$result->finalize();
					$query->close();
				}

				$db->close();

				$filename = $_FILES['file']['name'];

				$dir = 'assets'.DS.'images'.DS.'users'.DS.$handle[0].DS.$handle[1].DS.$handle[2].DS.$handle.DS.'sandbox'.DS.$id;

				mkdir($dir);

				$location = $dir.'/thumb.png';
				$imageFileType = pathinfo($location,PATHINFO_EXTENSION);
				$imageFileType = strtolower($imageFileType);

				$valid_extensions = array('png');
				
				if(in_array(strtolower($imageFileType), $valid_extensions)) {
					move_uploaded_file($_FILES['file']['tmp_name'], $location);
				}

				$file = SYS.'db/chat/blank.db';
				$newfile = SYS.'db/chat/'.$id.'.db';

				if (!copy($file, $newfile)) {
				    die();
				}

				echo $id;
			}
		}
	}
}