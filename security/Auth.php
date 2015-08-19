<?php

class Auth {

		public static function attempt($password, $username, $dbc) {
			$select = "SELECT id, user_name, password FROM users 
						WHERE user_name = :username";

			$stmt = $dbc->prepare($select);

			$stmt->bindValue(':username', $username, PDO::PARAM_STR);
			
	 		$stmt->execute();

			$credentials = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			if (password_verify($password, $credentials[0]["password"])) {
				$_SESSION["id"] = $credentials[0]["id"];
				return true;
			} else {
				return false;
			}
		}
	public static function check() {
		if (isset($_SESSION["username"]) && $_SESSION["username"] == "timothy") {
			return true;
		} else {
			return false;
		}
	}
	public static function user() {
		if (self::check()) {
			return $_SESSION["username"];
		}
	}
	public static function logout() {
		$_SESSION = array();
		session_destroy();
	}
}