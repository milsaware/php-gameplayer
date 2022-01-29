<?php
class authModel {

	public static function fetchUser($id){
		$id = preg_replace('/[^0-9]+/', '', $id);
		$data = array();
		$db = new SQLite3(SYS.'db'.DS.'users.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT username, handle, url
			FROM ud
			WHERE uid = :uid
			LIMIT 1
		';

		if($query = $db->prepare($query)){
			$query->bindValue(':uid', $id, SQLITE3_INTEGER);
			$result = $query->execute();

			while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
				$data[] = array(
					'username' => $row['username'],
					'handle' => $row['handle'],
					'url' => $row['url']
				);
			}
			

			$result->finalize();
			$query->close();
		}

		$db->close();

		return $data;
	}

	public static function confirm($type, $username){
		$type = preg_replace('/[^a-z]+/', '', $type);
		$username = strtolower($username);
		$data = 0;
		$db = new SQLite3(SYS.'db'.DS.'users.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT handle
			FROM ud
			WHERE handle = "'.$username.'"
			LIMIT 1
		';

		if($query = $db->prepare($query)){
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

	public static function recConfirm($username, $key){
		$data = 0;
		$username = strtolower($username);
		$key_1 = hash('sha256', $username);
		$key_3 = hash('sha256', $key);
		$db = new SQLite3(SYS.'db'.DS.'users.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT key_1, key_3
			FROM ukeys
			WHERE key_1 = "'.$key_1.'"
			AND key_3 = "'.$key_3.'"
			LIMIT 1
		';

		if($query = $db->prepare($query)){
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

	public static function sessionVerify($uid, $key, $step=1){
		$data = 0;
		$and = ($step == 0)? '' : 'AND value = :value';
		$db = new SQLite3(SYS.'db'.DS.'users.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT key
			FROM session
			WHERE key = :key
			'.$and.'
			LIMIT 1
		';

		if($query = $db->prepare($query)){
			$result = $query->execute();
			$query->bindValue(':key', $key, SQLITE3_TEXT);
			if($step == 1){
				$query->bindValue(':value', $uid, SQLITE3_INTEGER);
			}
			$query->execute();

			while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
				$data = 1;
			}

			$result->finalize();
			$query->close();
		}

		$db->close();

		return $data;
	}
	
	public static function authSubmit($name, $handle, $password){
		$data = 0;
		$handle = strtolower($handle);
		$key_1 = hash('sha256', $handle);
		$key_2 = hash('sha256', $handle.$password);
		$db = new SQLite3(SYS.'db'.DS.'users.db', SQLITE3_OPEN_READWRITE);
		$query = '
			SELECT key_1, key_2
			FROM ukeys
			WHERE key_1 = "'.$key_1.'"
			AND key_2 = "'.$key_2.'"
			LIMIT 1
		';

		if($query = $db->prepare($query)){
			$result = $query->execute();

			if($row = $result->fetchArray(SQLITE3_ASSOC) ) {
				$data = 1;
			}

			$result->finalize();
			$query->close();
		}
		
		if($data == 1){
			$query = '
				SELECT uid
				FROM ud
				WHERE handle = "'.$handle.'"
				LIMIT 1
			';

			if($query = $db->prepare($query)){
				$result = $query->execute();

				while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
					$uid = $row['uid'];
					$key = hash('sha256', date('dmy').$key_1.$key_2.date('his'));
					$_SESSION['auth'] = $key;
					$_SESSION['uid'] = $uid;
					$val = 0;
					$timestamp = date('ymdHis');
					
					$check_query = 'SELECT value FROM session WHERE value = "'.$uid.'" LIMIT 1';
					if($check_query = $db->prepare($check_query)){
						$check_result = $check_query->execute();

						if($row = $check_result->fetchArray(SQLITE3_ASSOC) ) {
							$val = 1;
						}

						$check_result->finalize();
						$check_query->close();
					}

					$sub_query = ($val == 0)? 'INSERT INTO "session" ("key", "value", "timestamp") VALUES (:key, :value, :timestamp)' : 'UPDATE session SET key = :key, value = :value, timestamp = :timestamp WHERE value = :wValue';
					if($sub_query = $db->prepare($sub_query)){
						$sub_query->bindValue(':key', $key, SQLITE3_TEXT);
						$sub_query->bindValue(':value', $uid, SQLITE3_TEXT);
						$sub_query->bindValue(':timestamp', $timestamp, SQLITE3_TEXT);
						if($val == 1){
							$sub_query->bindValue(':wValue', $uid, SQLITE3_INTEGER);
						}
						$sub_query->execute();
						$sub_query->close();
					}
				}

				$result->finalize();
				$query->close();
			}
		}

		$db->close();

		return $data;
	}
	
	public static function registerSubmit($name, $username, $password){
		$userlow = str_replace(' ', '_', strtolower($username));
		$rsc = rand(75,150);
		$rand = random_string($rsc);
		$_SESSION['key'] = $rand;
		$key_1 = hash('sha256', $userlow);
		$key_2 = hash('sha256', $userlow.$password);
		$key_3 = hash('sha256', $rand);
		$db = new SQLite3(SYS.'db'.DS.'users.db', SQLITE3_OPEN_READWRITE);

		$query = 'INSERT INTO "ukeys" ("key_1", "key_2", "key_3") VALUES (:key_1, :key_2, :key_3)';
		if($query = $db->prepare($query)){
			$query->bindValue(':key_1', $key_1, SQLITE3_TEXT);
			$query->bindValue(':key_2', $key_2, SQLITE3_TEXT);
			$query->bindValue(':key_3', $key_3, SQLITE3_TEXT);
			$query->execute();
			$query->close();
		}

		$query = 'INSERT INTO "ud" ("username", "handle") VALUES (:username, :handle)';
		if($query = $db->prepare($query)){
			$query->bindValue(':username', $name, SQLITE3_TEXT);
			$query->bindValue(':handle', $userlow, SQLITE3_TEXT);
			$query->execute();
			$query->close();
		}

		$db->close();

		createDir(SYS.'db'.DS.'users'.DS.$userlow[0]);
		createDir(SYS.'db'.DS.'users'.DS.$userlow[0].DS.$userlow[1]);
		createDir(SYS.'db'.DS.'users'.DS.$userlow[0].DS.$userlow[1].DS.$userlow[2]);

		$db = new SQLite3(SYS.'db'.DS.'users'.DS.$userlow[0].DS.$userlow[1].DS.$userlow[2].DS.$userlow.'.db');

		$query = 'CREATE TABLE comments_liked (game_id INTEGER PRIMARY KEY UNIQUE NOT NULL, comment_id STRING)';
		if($query = $db->prepare($query)){
			$query->execute();
			$query->close();
		}

		$query = 'CREATE TABLE comments_removed (game_id INTEGER PRIMARY KEY UNIQUE NOT NULL, comment_id STRING)';
		if($query = $db->prepare($query)){
			$query->execute();
			$query->close();
		}

		$query = 'CREATE TABLE following (like_id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE, uid INTEGER UNIQUE)';
		if($query = $db->prepare($query)){
			$query->execute();
			$query->close();
		}

		$query = 'CREATE TABLE games_info (id INTEGER PRIMARY KEY UNIQUE, title TEXT, description TEXT, url TEXT, embed TEXT, cat INTEGER, offline INT)';
		if($query = $db->prepare($query)){
			$query->execute();
			$query->close();
		}

		$query = 'CREATE TABLE games_liked (like_id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE, game_id INTEGER UNIQUE)';
		if($query = $db->prepare($query)){
			$query->execute();
			$query->close();
		}

		$query = 'CREATE TABLE games_notliked (like_id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE, game_id INTEGER UNIQUE)';
		if($query = $db->prepare($query)){
			$query->execute();
			$query->close();
		}

		$query = 'CREATE TABLE games_stats (game_id INTEGER PRIMARY KEY UNIQUE, imp INTEGER, hits INTEGER, likes INTEGER, downloads INTEGER)';
		if($query = $db->prepare($query)){
			$query->execute();
			$query->close();
		}

		$query = 'CREATE TABLE offline (game_id INTEGER PRIMARY KEY UNIQUE, price DECIMAL (26, 2), hash VARCHAR (64))';
		if($query = $db->prepare($query)){
			$query->execute();
			$query->close();
		}

		$query = 'CREATE TABLE profile ("key", value)';
		if($query = $db->prepare($query)){
			$query->execute();
			$query->close();
		}

		$query = 'CREATE TABLE sandbox (id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE, title TEXT, description TEXT, url TEXT, embed TEXT, cat INTEGER, offline INT, req INT)';
		if($query = $db->prepare($query)){
			$query->execute();
			$query->close();
		}

		$query = 'INSERT INTO profile ("key", value) VALUES ("profile_content", NULL)';
		if($query = $db->prepare($query)){
			$query->execute();
			$query->close();
		}

		$query = 'INSERT INTO profile ("key", value) VALUES ("public", "0"),("profile_content", ""),("banner", "red"),("profile_url", "default"),("username", :name)';
		if($query = $db->prepare($query)){
			$query->bindValue(':name', $name, SQLITE3_TEXT);
			$query->execute();
			$query->close();
		}

		$db->close();

		createDir('assets'.DS.'images'.DS.'users'.DS.$userlow[0]);
		createDir('assets'.DS.'images'.DS.'users'.DS.$userlow[0].DS.$userlow[1]);
		createDir('assets'.DS.'images'.DS.'users'.DS.$userlow[0].DS.$userlow[1].DS.$userlow[2]);
		createDir('assets'.DS.'images'.DS.'users'.DS.$userlow[0].DS.$userlow[1].DS.$userlow[2].DS.$userlow);
		createDir('assets'.DS.'images'.DS.'users'.DS.$userlow[0].DS.$userlow[1].DS.$userlow[2].DS.$userlow.DS.'sandbox');

		$imgFrm = 'assets'.DS.'images'.DS.'users'.DS.'default'.DS.'avatar.png';
		$imgTo = 'assets'.DS.'images'.DS.'users'.DS.$userlow[0].DS.$userlow[1].DS.$userlow[2].DS.$userlow.DS.'avatar.png';
		copy($imgFrm, $imgTo);

		echo authModel::authSubmit($name, $username, $password);
	}
	
	public static function changePassword($username, $key, $password){
		$userlow = strtolower($username);
		$rsc = rand(75,150);
		$rand = random_string($rsc);
		$_SESSION['key'] = $rand;
		$key = hash('sha256', $key);
		$key_1 = hash('sha256', $userlow);
		$key_2 = hash('sha256', $userlow.$password);
		$key_3 = hash('sha256', $rand);
		$db = new SQLite3(SYS.'db'.DS.'users.db', SQLITE3_OPEN_READWRITE);

		$query = '
			UPDATE ukeys
			SET key_2 = :key_2, key_3 = :key_3
			WHERE key_1 = :key_1
			AND key_3 = :key
		';

		if($query = $db->prepare($query)){
			$query->bindValue(':key', $key, SQLITE3_TEXT);
			$query->bindValue(':key_1', $key_1, SQLITE3_TEXT);
			$query->bindValue(':key_2', $key_2, SQLITE3_TEXT);
			$query->bindValue(':key_3', $key_3, SQLITE3_TEXT);
			$query->execute();
			$query->close();
		}

		$db->close();
	}
	
}