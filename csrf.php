<?php // Code found at http://www.wikihow.com/Prevent-Cross-Site-Request-Forgery-(CSRF)-Attacks-in-PHP
class csrf {
	// This obtains the token id and returns it given the users session, and if no user session exists it will create a new token id
	public function get_token_id() {
		if (isset($_SESSION['token_id'])) {
			return $_SESSION['token_id'];
		} else {
			$token_id = $this->random(10);
			$_SESSION['token_id'] = $token_id;
			return $token_id;
		}
	}
	
	// Return the token value if user session exists, and if no user session exists it will create a token using our random function and a sha256 hash
	public function get_token() {
		if (isset($_SESSION['token_value'])) {
			return $_SESSION['token_value'];
		} else {
			$token = hash('sha256', $this->random(500));
			$_SESSION['token_value'] = $token;
			return $token;
		}
	}
	
	// This will return a boolean depending on if the token is valid or not
	public function check_valid($method) {
		if ($method == 'post' || $method == 'get') {
			$post = $_POST;
			$get = $_GET;
			if (isset(${$method}[$this->get_token_id()]) && (${$method}[$this->get_token_id()] == $this->get_token())) {
				return true;
			}
			return false;
		}
		return false;
	}
	
	// This function attempts to prevent csrf by generating random form field names as a second line of defense if the token is obtained
	public function form_names($names, $regenerate) {
		$values = array();
		foreach ($names as $n) {
			if ($regenerate == true) {
				unset($_SESSION[$n]);
			}
			$s = isset($_SESSION[$n]) ? $_SESSION[$n] : $this->random(10);
			$_SESSION[$n] = $s;
			$values[$n] = $s;
		}
		return $values;
	}
	
	// A function that is given a desired length and generates a random string based off of the length
	private function random($len) {
		if (function_exists('openssl_random_pseudo_bytes')) {
			$byteLen = intval(($len / 2) + 1);
			$return = substr(bin2hex(openssl_random_pseudo_bytes($byteLen)), 0, $len); // generates random string given byte len. converts binary to hex, then constrains the length of the end string to the given length
		} elseif (@is_readable('/dev/urandom')) {
			$f = fopen('/dev/urandom', 'r');
			$urandom = fread($f, $len);
			fclose($f);
			$return = '';
		}
		
		if (empty($return)) { // A lengthier alternative to using openssl_random_pseudo_bytes in the case that it does not exist.
			for ($i=0; $i<$len; ++$i) {
				if (!isset($urandom)) {
					if ($i%2 == 0) {
						mt_srand(time()%2147 * 1000000 + (double)microtime() * 1000000);
					}
					$rand = 48 + mt_rand()%64;
				} else {
					$rand = 48 + ord($urandom[$i])%64;
				}
				
				if ($rand > 57) {
					$rand += 7;
				}
				if ($rand > 90) {
					$rand += 6;
				}
				if ($rand == 123) {
					$rand = 52;
				}
				if ($rand == 124) {
					$rand = 53;
				}
				$return .= chr($rand);
			}
		}
		return $return;
	}
}