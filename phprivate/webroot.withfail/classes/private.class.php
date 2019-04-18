<?php

class PrivateEnv {
	public function upload($username, $content) {
		global $db;
		if(isset($_FILES['file'])) {
			$stmt = mysqli_prepare($db,"INSERT INTO files (username, name) VALUES (?,?)");
			mysqli_stmt_bind_param($stmt,'ss', $username, hash('sha256',$content));
			mysqli_stmt_execute($stmt);
			move_uploaded_file($_FILES['file']['tmp_name'],'/var/www/files/'.hash('sha256',$content));
		}
		return false;
	}

	public function retrieve($name) {
		global $db;
		$stmt = mysqli_prepare($db,"SELECT * FROM files WHERE name = ?");
		mysqli_stmt_bind_param($stmt,'s', $name);
		mysqli_stmt_execute($stmt);
		return mysqli_fetch_all(mysqli_stmt_get_result($stmt),MYSQLI_ASSOC)[0];
	}

	public function download($name) {
		$file = $this->retrieve($name);
		$path = "/var/www/files/".$file['name'];
		return file_get_contents($path);
	}

	public function list($username) {
		global $db;
		$stmt = mysqli_prepare($db,"SELECT * FROM files WHERE username = ?");
		mysqli_stmt_bind_param($stmt,'s', $username);
		mysqli_stmt_execute($stmt);
		return mysqli_fetch_all(mysqli_stmt_get_result($stmt),MYSQLI_ASSOC);
	}

	public function count($username) {
		return count($this->list($username));
	}
}