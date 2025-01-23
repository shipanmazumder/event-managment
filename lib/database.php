<?php
class database {
	public $host = DB_HOST;
	public $user = DB_USER;
	public $pass = DB_PASS;
	public $dbname = DB_NAME;

	public $link;
	public $error;

	public function __construct() {
		$this->connectDB();
	}

	private function connectDB() {
		try {
			if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_PASS') || !defined('DB_NAME')) {
				throw new Exception("Database configuration constants are not defined");
			}

			// Attempt connection
			$this->link = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
			
			if ($this->link->connect_errno) {
				throw new Exception($this->link->connect_error);
			}
		} catch (\Exception $e) {
			$this->error = "Connection Error: " . $e->getMessage();
			die($this->error);
		}
	}

	public function select($query) {
		$result = $this->link->query($query) or die($this->link->error.__LINE__);
		if ($result->num_rows > 0) {
			return $result;
		} else {
			return false;
		}
	}

	public function insert($create) {
		$insert_row = $this->link->query($create) or die($this->link->error.__LINE__);
		if ($insert_row) {
			return $insert_row;
		} else {
			return false;
		}
	}

	public function update($update) {
		$update_row = $this->link->query($update) or die($this->link->error.__LINE__);
		if ($update_row) {
			return $update_row;
		} else {
			return false;
		}
	}

	public function delete($delete) {
		$delete_row = $this->link->query($delete) or die($this->link->error.__LINE__);
		if ($delete_row) {
			return $delete_row;
		} else {
			return false;
		}
	}
}
?>
