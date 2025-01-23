<?php
	/**
	* session class
	*/
	class session
	{
		public static function init(){
			if (session_status() === PHP_SESSION_NONE) {
				session_start();
			}
		}
		public static function set($key,$val){
			$_SESSION[$key]=$val;
		}
		public static function get($key){
			 if(isset($_SESSION[$key])){
			 	return $_SESSION[$key];
			 }
			 else{
			 	return false;
			 }
		}
		public static function checkSession(){
			self::init();
			if(self::get('adminlogin')==false){
				self::destory();
				header("Location:login.php");
			}
		}
		public static function checklogin(){
			self::init();
			if(self::get('adminlogin')==true){
				header("Location:index.php");
			}
		}
		public static function destory(){
			try {
				
				session_destroy();
				header("Location:login.php");
			} catch (Exception $e) {
				echo "Session Destroy Error: " . $e;
				exit();
			}
		}
		
	}
?>
