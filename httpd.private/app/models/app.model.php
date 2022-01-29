<?php
include_model('auth');
use authModel as auth;
class appModel {

	public static function fetchHome($cat){
		$cat = preg_replace("/[^0-9]/", "", $cat);
		$data = array();
		$db = new SQLite3(SYS.'db'.DS.'games.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT id, title, url, cat
			FROM info
			WHERE cat = :cat
			ORDER BY RANDOM()
		';

		if($query = $db->prepare($query)){
			$update_query->bindValue(':cat', $cat, SQLITE3_TEXT);
			$result = $query->execute();

			while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
				$data[] = array(   
					'id' => $row['id'],
					'title' => $row['title'],
					'url' => $row['url'],
					'cat' => $row['cat']
				);
			}

			$result->finalize();
			$query->close();
		}

		$db->close();

		return $data;
	}

	public static function fetchAll(){
		$data = array();
		$db = new SQLite3(SYS.'db'.DS.'games.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT info.id, info.title, info.url
			FROM info
			INNER JOIN att
			ON info.id = att.id
			ORDER BY info.id ASC
		';

		if($query = $db->prepare($query)){
			$result = $query->execute();

			while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
				$data[] = array(   
					'id' => $row['id'],
					'title' => $row['title'],
					'url' => $row['url']
				);
			}

			$result->finalize();
			$query->close();
		}

		$db->close();

		return $data;
	}

	public static function fetchGame($id){
		$data = array();
		$db = new SQLite3(SYS.'db'.DS.'games.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT info.id, info.title, info.url, info.embed, att.author_id
			FROM info
			INNER JOIN att
			ON info.id = att.id
			WHERE info.id = :id
			LIMIT 1
		';

		if($query = $db->prepare($query)){
			$query->bindValue(':id', $id, SQLITE3_INTEGER);
			$result = $query->execute();

			while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
				$data[] = array(   
					'id' => $row['id'],
					'title' => $row['title'],
					'url' => $row['url'],
					'embed' => $row['embed'],
					'author_id' => $row['author_id']
				);
				$uid = $row['author_id'];
			}

			$result->finalize();
			$query->close();
		}

		$db->close();

		return $data;
	}
	
	public static function fetchSliderCats(){
		$data = array();
		$db = new SQLite3(SYS.'db'.DS.'games.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT id, key
			FROM cats
			ORDER BY id ASC
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

	public static function fetchWhere($ids){
		$data = array();
		$i = 1;
		$where = '';
		foreach($ids as $id){
			$id = preg_replace('/[^1-9]+/', '', $id);
			if(is_numeric($id) && $id != ''){
				$where .= ($i == 1)? 'WHERE id = :id'.$i : ' OR id = :id'.$i;
				$i++;
			}
		}
		$db = new SQLite3(SYS.'db'.DS.'games.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT id, title, url, embed
			FROM info
			'.$where.'
		';

		if($query = $db->prepare($query)){
			$i = 1;
			foreach($ids as &$id){
				$id = preg_replace('/[^1-9]+/', '', $id);
				if(is_numeric($id) && $id != ''){
					$query->bindParam(':id'.$i, $id);
					$i++;
				}
			}
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

	public static function submitPost($title){
		$url = strtolower($title);
		$url = preg_replace("/[^a-z0-9 ]/", "", $url);
		$url = preg_replace("/[^a-z0-9]/", "_", $url);
		
		$db = new SQLite3(SYS.'db'.DS.'games.db', SQLITE3_OPEN_READWRITE);
		$query = 'INSERT INTO "info" ("title", "url") VALUES (:title, :url)';
		$query = $db->prepare($query);
		$query->bindValue(':title', $title);
		$query->bindValue(':url', $url);
		$query->execute();
		$query->close();

		$db->close();
		return 'success! '.$title.' added';
	}

	public static function fetchGameLikes($game_id){
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
				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$f1.DS.$f2.DS.$f3.DS.$handle.'.db', SQLITE3_OPEN_READONLY);
				$query = '
					SELECT game_id
					FROM games_liked
					WHERE game_id = :game_id
					LIMIT 1
				';

				if($query = $db->prepare($query)){
					$query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
					$result = $query->execute();
					if($row = $result->fetchArray(SQLITE3_ASSOC)){
						$return = 1;
						$result->finalize();
						$query->close();
						$db->close();
					}else{
						$query = '
							SELECT game_id
							FROM games_notliked
							WHERE game_id = :game_id
							LIMIT 1
						';
						if($query = $db->prepare($query)){
						$query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
							$result = $query->execute();
							if($row = $result->fetchArray(SQLITE3_ASSOC)){
								$return = 2;
								$result->finalize();
								$query->close();
								$db->close();
							}
						}
					}
				}
			}
		}

		return $return;
	}

	public static function fetchCommentLikes($game_id){
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
				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$f1.DS.$f2.DS.$f3.DS.$handle.'.db', SQLITE3_OPEN_READONLY);
				$query = '
					SELECT game_id
					FROM games_liked
					WHERE game_id = :game_id
					LIMIT 1
				';

				if($query = $db->prepare($query)){
					$query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
					$result = $query->execute();
					if($row = $result->fetchArray(SQLITE3_ASSOC)){
						$return = 1;
						$result->finalize();
						$query->close();
						$db->close();
					}else{
						$query = '
							SELECT game_id
							FROM games_notliked
							WHERE game_id = :game_id
							LIMIT 1
						';
						if($query = $db->prepare($query)){
						$query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
							$result = $query->execute();
							if($row = $result->fetchArray(SQLITE3_ASSOC)){
								$return = 2;
								$result->finalize();
								$query->close();
								$db->close();
							}
						}
					}
				}
			}
		}

		return $return;
	}

	public static function setFav($id, $type){
		if(isset($_SESSION['auth']) && isset($_SESSION['uid'])){
			$key = str_replace(' ', '', $_SESSION['auth']);
			$uid = preg_replace('/[^0-9]+/', '', $_SESSION['uid']);
			if($key !== 0 && auth::sessionVerify($uid, $key) == 1){
				$table = ($type == 'like')? 'games_liked' : 'games_notliked';
				$deltable = ($type == 'like')? 'games_notliked' : 'games_liked';
				$userDetails = auth::fetchUser($uid);
				foreach($userDetails as $row){
					$username = $row['username'];
					$handle = $row['handle'];
				}
				$f1 = $handle[0];
				$f2 = $handle[1];
				$f3 = $handle[2];
				$q = 'INSERT INTO "'.$table.'" ("game_id") VALUES (:game_id)';
				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$f1.DS.$f2.DS.$f3.DS.$handle.'.db', SQLITE3_OPEN_READONLY);
				$query = '
					SELECT game_id
					FROM '.$table.'
					WHERE game_id = :game_id
					LIMIT 1
				';

				if($query = $db->prepare($query)){
					$query->bindValue(':game_id', $id);
					$result = $query->execute();
					if($row = $result->fetchArray(SQLITE3_ASSOC)){
						$q = 'DELETE FROM "'.$table.'" WHERE "game_id" = :game_id';
						$result->finalize();
						$query->close();
						$db->close();
					}
				}

				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$f1.DS.$f2.DS.$f3.DS.$handle.'.db', SQLITE3_OPEN_READWRITE);
				$query = $q;
				$query = $db->prepare($query);
				$query->bindValue(':game_id', $id);
				$query->execute();
				$query->close();

				$query = 'DELETE FROM "'.$deltable.'" WHERE "game_id" = :game_id';
				$query = $db->prepare($query);
				$query->bindValue(':game_id', $id);
				$query->execute();
				$query->close();

				$db->close();
			}
		}
	}
}
