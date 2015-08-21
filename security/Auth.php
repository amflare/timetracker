<?php

class Auth {

		// attempt only works with admin login. Student login is handled with waterfall code in /student_login.php
		public static function attempt($password, $email, $dbc) {
			$select = "SELECT id, password FROM admins WHERE email = :email";

			$stmt = $dbc->prepare($select);

			$stmt->bindValue(':email', $email, PDO::PARAM_STR);
			
	 		$stmt->execute();

			$credentials = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			if (password_verify($password, $credentials[0]["password"])) {
				$_SESSION["id"] = $credentials[0]["id"];
				$_SESSION["rank"] = "admin";
				return true;
			} else {
				return false;
			}
		}
	public static function check() {
		if (isset($_SESSION["id"]) && isset($_SESSION["rank"]) && $_SESSION["rank"] == $_SESSION["pageRank"]) {
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