<?php

class User {

	public function get($username) {
		global $db;
		$stmt = mysqli_prepare($db,"SELECT * FROM users WHERE login = ?");
		mysqli_stmt_bind_param($stmt,'s', $username);
		mysqli_stmt_execute($stmt);
		return mysqli_stmt_get_result($stmt);		
	}

	public function exists($username) {
		global $db;
		$user = $this->get($username);
		return ($user->num_rows !== 0);
	}

	public function login($username, $password){
		global $db;
		if($this->exists($username)){
			$user = mysqli_fetch_all($this->get($username), MYSQLI_ASSOC)[0];
			if($user && $user['password'] === md5($password)) {
				$_SESSION['logged_in_as'] = $user['login'];
				return true;
			} else {
				return false;
			}
		}
		return false;
	}

	public function register($username, $password){
		global $db;
		if(!$this->exists($username)) {
			$stmt = mysqli_prepare($db,"INSERT INTO users (login, password, isResetting) VALUES (?,?,0)");
			mysqli_stmt_bind_param($stmt,'ss', $username, md5($password));
			mysqli_stmt_execute($stmt);
			return true;
		}
		return false;
	}

	public function askOneTimePassword($username){
		global $db;
		if($this->exists($username)){
			$stmt = mysqli_prepare($db,"UPDATE users SET isResetting = 1 WHERE login = ?");
			mysqli_stmt_bind_param($stmt,'s',$username);
			mysqli_stmt_execute($stmt);
			$stmt = mysqli_prepare($db,"REPLACE INTO users_reset(login, token) VALUES (?,?)");
			mysqli_stmt_bind_param($stmt,'ss',$username,bin2hex(random_bytes(32)));
			mysqli_stmt_execute($stmt);
			/* We don't have any mailer at the moment, so it's not used */
		}
	}

	public function oneTimeLogin($username, $token){
		global $db;
		if($this->exists($username)){
			$user = mysqli_fetch_all($this->get($username), MYSQLI_ASSOC)[0];
			if($user['isResetting'] == '1') {
				$stmt = mysqli_prepare($db,"SELECT token FROM users_reset WHERE login = ? OR trim(login) = ? ");
				mysqli_stmt_bind_param($stmt,'ss',$username);
				mysqli_stmt_execute($stmt);
				$validToken = (string)mysqli_fetch_all(mysqli_stmt_get_result($stmt),MYSQLI_ASSOC)[0]['token'];
				if($validToken === $token) {
					$stmt = mysqli_prepare($db,"UPDATE users SET isResetting = 0 WHERE login = ?");
					mysqli_stmt_bind_param($stmt,'s',$user['login']);
					mysqli_stmt_execute($stmt);
					$stmt = mysqli_prepare($db,"DELETE FROM users_reset WHERE login = ?");
					mysqli_stmt_bind_param($stmt,'s',$user['login']);
					mysqli_stmt_execute($stmt);
					$_SESSION['logged_in_as'] = $user['login'];
				} else {
					return false;
				}
			}
		}
	}
}
