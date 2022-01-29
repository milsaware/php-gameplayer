<?php
include_model('auth');
use authModel as auth;
class chatModel {

	public static function fetchChat($id){
		$data = array();
		$id = preg_replace("/[^0-9]/", "", $id);
		$db = new SQLite3(SYS.'db'.DS.'chat'.DS.$id.'.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT id, par_id, message, author, timestamp, like_count, resp_count
			FROM content
			WHERE par_id = 0
			ORDER BY id DESC
		';

		if($query = $db->prepare($query)){
			$result = $query->execute();

			while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
				$data[] = array(   
					'id' => $row['id'],
					'par_id' => $row['par_id'],
					'message' => $row['message'],
					'author' => $row['author'],
					'timestamp' => $row['timestamp'],
					'like_count' => $row['like_count'],
					'resp_count' => $row['resp_count']
				);
			}

			$result->finalize();
			$query->close();
		}

		$db->close();

		return $data;
	}

	public static function fetchCommentsRemoved($game_id){
		if(isset($_SESSION['auth']) && isset($_SESSION['uid'])){
			$key = str_replace(' ', '', $_SESSION['auth']);
			$uid = preg_replace('/[^0-9]+/', '', $_SESSION['uid']);
			if($key !== 0 && auth::sessionVerify($uid, $key) == 1){
				$userDetails = auth::fetchUser($uid);
				foreach($userDetails as $row){
					$handle = $row['handle'];
				}
				$data = '';
				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$handle[0].DS.$handle[1].DS.$handle[2].DS.$handle.'.db', SQLITE3_OPEN_READONLY);
				$query = '
					SELECT comment_id
					FROM comments_removed
					WHERE game_id = :game_id
					LIMIT 1
				';

				if($query = $db->prepare($query)){
					$query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
					$result = $query->execute();

					while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
						$data = $row['comment_id'];
					}

					$result->finalize();
					$query->close();
				}

				$db->close();

				return $data;
			}
		}
	}

	public static function fetchComments($game_id, $comment_id, $type){
		$data = array();
		$game_id = preg_replace("/[^0-9]/", "", $game_id);
		$comment_id = preg_replace("/[^0-9]/", "", $comment_id);
		$where = ($type == 'parent')? 'id = :comment_id' : 'par_id = :comment_id';
		$limit = ($type == 'parent')? 'LIMIT 1' : '';
		$db = new SQLite3(SYS.'db'.DS.'chat'.DS.$game_id.'.db', SQLITE3_OPEN_READONLY);
		$query = '
			SELECT id, par_id, message, author, timestamp, like_count, resp_count
			FROM content
			WHERE '.$where.'
			ORDER BY id DESC
			'.$limit.'
		';

		if($query = $db->prepare($query)){
			$query->bindValue(':comment_id', $comment_id, SQLITE3_INTEGER);
			$result = $query->execute();

			while ($row = $result->fetchArray(SQLITE3_ASSOC) ) {
				$data[] = array(   
					'id' => $row['id'],
					'par_id' => $row['par_id'],
					'message' => $row['message'],
					'author' => $row['author'],
					'timestamp' => $row['timestamp'],
					'like_count' => $row['like_count'],
					'resp_count' => $row['resp_count']
				);
			}

			$result->finalize();
			$query->close();
		}

		$db->close();

		return $data;
	}
	
	public static function fetchCommentsSettings($game_id, $type){
		$comment_id = '';
		$game_id = preg_replace('/[^0-9]+/', '', $game_id);
		$db = new SQLite3(SYS.'db'.DS.'users'.DS.username[0].DS.username[1].DS.username[2].DS.username.'.db', SQLITE3_OPEN_READWRITE);
		$query = '
			SELECT comment_id
			FROM '.$type.'
			WHERE game_id = :game_id
			LIMIT 1
		';

		if($query = $db->prepare($query)){
			$query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
			$result = $query->execute();
			while($row = $result->fetchArray(SQLITE3_ASSOC)){
				$comment_id = $row['comment_id'];
			}

			$result->finalize();
			$query->close();
		}
		
		$db->close();

		return $comment_id;
	}
	
	public static function newComment($game_id, $message, $timestamp, $author){
		$game_id = preg_replace('/[^0-9]+/', '', $game_id);

		$db = new SQLite3(SYS.'db'.DS.'chat'.DS.$game_id.'.db', SQLITE3_OPEN_READWRITE);
		$query = 'INSERT INTO "content" ("message", "author", "timestamp") VALUES (:message, :author, :timestamp)';
		if($query = $db->prepare($query)){
			$query->bindValue(':message', $message, SQLITE3_TEXT);
			$query->bindValue(':author', $author, SQLITE3_TEXT);
			$query->bindValue(':timestamp', $timestamp, SQLITE3_INTEGER);
			$query->execute();
			$query->close();
		}
		$db->close();
	}
	
	public static function commentRespond($parent_id, $game_id, $message, $author){
		$parent_id = preg_replace('/[^0-9]+/', '', $parent_id);
		$game_id = preg_replace('/[^0-9]+/', '', $game_id);

		$timestamp = time();
		$db = new SQLite3(SYS.'db'.DS.'chat'.DS.$game_id.'.db', SQLITE3_OPEN_READWRITE);
		$query = 'INSERT INTO "content" ("par_id", "message", "author", "timestamp") VALUES (:parent_id, :message, :author, :timestamp)';
		if($query = $db->prepare($query)){
			$query->bindValue(':parent_id', $parent_id, SQLITE3_INTEGER);
			$query->bindValue(':message', $message, SQLITE3_TEXT);
			$query->bindValue(':author', $author, SQLITE3_TEXT);
			$query->bindValue(':timestamp', $timestamp, SQLITE3_INTEGER);
			$query->execute();
			$query->close();
		}
		$db->close();
	}
	
	public static function setCommentPref($game_id, $comment_id, $type){
		$comment_id = preg_replace('/[^0-9]+/', '', $comment_id);
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
				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$f1.DS.$f2.DS.$f3.DS.$handle.'.db', SQLITE3_OPEN_READWRITE);
				$query = '
					SELECT comment_id
					FROM '.$type.'
					WHERE game_id = :game_id
					LIMIT 1
				';

				if($query = $db->prepare($query)){
					$query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
					$result = $query->execute();
					if($row = $result->fetchArray(SQLITE3_ASSOC)){
						$id_array = explode(',', $row['comment_id']);
						if(!in_array($comment_id, $id_array)){
							$id_string = $row['comment_id'].','.$comment_id;				
							$update_query = 'UPDATE '.$type.' SET comment_id = :comment_id WHERE game_id = :game_id';
							if($update_query = $db->prepare($update_query)){
								$update_query->bindValue(':comment_id', $id_string, SQLITE3_TEXT);
								$update_query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
								$update_query->execute();
								$update_query->close();
							}

							if($type == 'comments_liked'){
								chatModel::updateLikeCount($comment_id, $game_id);
							}
						}

						$result->finalize();
						$query->close();
						$db->close();
					}

					else{
						$query = 'INSERT INTO "'.$type.'" ("game_id", "comment_id") VALUES (:game_id, :comment_id)';
						if($query = $db->prepare($query)){
							$query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
							$query->bindValue(':comment_id', $comment_id, SQLITE3_INTEGER);
							$query->execute();
							$query->close();
						}

						if($type == 'comments_liked'){
							chatModel::updateLikeCount($comment_id, $game_id);
						}
						$db->close();
					}
				}
			}
		}
	}

	public static function removeCommentLike($comment_id, $game_id){
		$comment_id = preg_replace('/[^0-9]+/', '', $comment_id);
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
				$db = new SQLite3(SYS.'db'.DS.'users'.DS.$f1.DS.$f2.DS.$f3.DS.$handle.'.db', SQLITE3_OPEN_READWRITE);
				$query = '
					SELECT comment_id
					FROM comments_liked
					WHERE game_id = :game_id
					LIMIT 1
				';

				if($query = $db->prepare($query)){
					$query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
					$result = $query->execute();
					if($row = $result->fetchArray(SQLITE3_ASSOC)){
						$newArray = '';
						$id_array = explode(',', $row['comment_id']);
						foreach($id_array as $row){
							if($comment_id != $row){
								$newArray .= $row.',';
							}
						}
						$newArray = substr($newArray, 0, -1);
					}
					$query->execute();
					$query->close();

					$query = '
						UPDATE comments_liked
						SET comment_id = :newArray
						WHERE game_id = :game_id
					';

					if($query = $db->prepare($query)){
						$query->bindValue(':newArray', $newArray, SQLITE3_TEXT);
						$query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
						$query->execute();
						$query->close();
					}
				}
			}
		}
	}
	
	public static function updateLikeCount($comment_id, $game_id){
			$db = new SQLite3(SYS.'db'.DS.'chat'.DS.$game_id.'.db', SQLITE3_OPEN_READWRITE);
			$query = '
				SELECT like_count
				FROM content
				WHERE id = :comment_id
				LIMIT 1
			';
			if($query = $db->prepare($query)){
				$query->bindValue(':comment_id', $comment_id, SQLITE3_INTEGER);
				$result = $query->execute();
				if($row = $result->fetchArray(SQLITE3_ASSOC)){
					$like_count = $row['like_count'] + 1;
					$query = 'UPDATE "content" SET like_count = :like_count WHERE id = :id';
					if($dba_update_query = $dba->prepare($dba_update_query)){
						$query->bindValue(':like_count', $like_count, SQLITE3_INTEGER);
						$query->bindValue(':id', $comment_id, SQLITE3_INTEGER);
						$query->execute();
						$query->close();
					}
				}
			}

			$result->finalize();
			$query->close();
			$db->close();
	}

	public static function fetchLikes($comment_id, $game_id){
		$return = 'false';
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
				$query = '
					SELECT comment_id
					FROM comments_liked
					WHERE game_id = :game_id
					LIMIT 1
				';

				if($query = $db->prepare($query)){
					$query->bindValue(':game_id', $game_id, SQLITE3_INTEGER);
					$result = $query->execute();
					if($row = $result->fetchArray(SQLITE3_ASSOC)){
						$id_array = explode(',', $row['comment_id']);
						if(in_array($comment_id, $id_array)){
							$return = 'true';
						}

						$result->finalize();
						$query->close();
						$db->close();
					}
				}
			}
		}

		return $return;
	}

	public static function fetchGameLikes($game_id){
		$return = 'false';
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
						$return = 'true';
						$result->finalize();
						$query->close();
						$db->close();
					}
				}
			}
		}

		return $return;
	}
}