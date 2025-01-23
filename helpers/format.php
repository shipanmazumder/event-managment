<?php
	class format
	{
		
		public function formatdate($date){
			return date('F j,Y, g:i:a',strtotime($date));
		}
		public function textshorten($text,$limit=400){
			$text=$text." ";
			$text=substr($text,0,$limit);
			$text=substr($text,0,strrpos($text, ' '));
			return $text=$text.".....";
		}
		public function valid($data){
			$data=trim("$data");
			$data=stripcslashes("$data");
			$data=htmlspecialchars("$data");
			return $data;
		}
		public function title(){
			$path=$_SERVER['SCRIPT_FILENAME'];
			$title=basename($path,'.php');
			$title=str_replace('-',' ', $title);
			if($title=='index'){
				$title='Dashboard';
			}
			if($title=='create-event'){
				$title='Event Create';
			}
			else if($title=='create-event'){
				$title='Event Create';
			}
			elseif($title=='login'){
				$title='login';
			}
			return $title=ucwords($title);
		}
	}
?>