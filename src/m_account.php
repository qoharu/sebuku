<?php 

class Accountmodel{
	function authToken(){
		$token = @$_GET['token'];
		$user_id = @$_GET['user_id'];
		$db = connect_db();
		$result = $db->query("SELECT * FROM user WHERE  `token` = '$token' AND `user_id` = '$user_id' ");
		if ($result->num_rows == 1) {
			$res['status'] = true;
			$res['message'] = "You've already logged in";
		}else{
			$res['status'] = false;
			$res['message'] = "Invalid Token, Please Re Login";
		}
		return $res;
	}

	function update_location($longitude,$latitude,$uid){
		$db = connect_db();
		$query = $db->query("UPDATE user SET longitude = '$longitude', latitude = '$latitude' WHERE user_id = '$uid'  ");
		if ($query) {
			$hasil['status'] = true;
			$hasil['message'] = "Location udated";
		}else{
			$hasil['status'] = false;
			$hasil['message'] = "Someting wrong";
		}
		
		return $hasil;
	}

	function register($data){
		$db = connect_db();
		$fullname = $data['fullname'];
		$email = $data['email'];
		$password = $data['password'];
		$token = md5(microtime());

		$query = $db->query("INSERT INTO user (fullname,email,password,token)
								VALUES ('$fullname','$email','$password','$token') ");
		if ($query) {
			$hasil = $this->auth($email,$password);
		}else{
			$hasil['status'] = false;
			$hasil['message'] = "Email already registered";
		}
		return $hasil;
	}

	function auth($email,$password){
		$password = $password;
		$db = connect_db();
		$result = $db->query("SELECT * FROM user WHERE  `email` = '$email' AND `password` = '$password' ");
		if ($result->num_rows == 1) {
			$newToken = sha1(microtime());
			$db->query("UPDATE user SET token='$newToken' where email='$email' ");
			$hasil = $result->fetch_assoc();
			$res['status'] = true;
			$res['message'] = "You're successfully logged in";
			$res['user_id'] = $hasil['user_id'];
			$res['fullname'] = $hasil['fullname'];
			$res['email'] = $hasil['email'];
			$res['user_id'] = $hasil['user_id'];
			$res['token'] = $newToken;
		}else{
			$res['status'] = false;
			$res['message'] = "Invalid Details, Please Re Login";
		}
		return $res;
	}

	function logout($uid){
		$db = connect_db();
		$reset = md5(microtime());
		$hasil = $db->query("UPDATE user SET token = NULL WHERE user_id = '$uid'  ");
		return $hasil;
	}
		
}
