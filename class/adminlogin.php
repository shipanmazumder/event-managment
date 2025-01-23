<?php
session::checklogin();
?>

<?php
class adminlogin {
    private $db;
    private $fm;

    public function __construct() {
        $this->db = new database();
        $this->fm = new format();
    }

    public function login($data) {
        try {
            $email = $data['email'];
            $password = $data['password'];
            $email = $this->fm->valid($email);
            $password = $this->fm->valid($password);
            if (empty($email) || empty($password)) {
                return '<span style="color:red;">Username Or Password Should Not Be Empty</span>';
            }

            $stmt = $this->db->link->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->link->error);
            }

            $stmt->bind_param("s", $email);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $stmt->close();

            if ($result && $user = $result->fetch_assoc()) {
                if (password_verify($password, $user['password'])) {
                    session::set("adminlogin", true);
                    session::set("email", $user['email']);
                    session::set("id", $user['id']);
                    session::set("name", $user['name']);
                    header("Location:index.php");
                    exit();
                }
            }
            
            return '<span style="color:red;">Username Or Password Did Not Match</span>';
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function register($data){
        try {
            $name = $this->fm->valid($data['name']);
            $email = $this->fm->valid($data['email']);
            $password = $this->fm->valid($data['password']);
            if (empty($name) || strlen($name) < 3 || !preg_match("/^[a-zA-Z\s]+$/", $name)) {
                return "Invalid name format";
            }
        
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return "Invalid email format";
            }
        
            if (empty($password) || strlen($password) < 8 || 
                !preg_match("/[A-Z]/", $password) || 
                !preg_match("/[a-z]/", $password) || 
                !preg_match("/[0-9]/", $password)) {
                return "Invalid password format";
            }
        
            $checkStmt = $this->db->link->prepare("SELECT email FROM admins WHERE email = ? LIMIT 1");
            if (!$checkStmt) {
                throw new Exception("Email check prepare failed: " . $this->db->link->error);
            }
        
            $checkStmt->bind_param("s", $email);
            
            if (!$checkStmt->execute()) {
                throw new Exception("Email check execute failed: " . $checkStmt->error);
            }
        
            $result = $checkStmt->get_result();
            $checkStmt->close();
        
            if ($result->num_rows > 0) {
                return '<span style="color:red;">Email already exists</span>';
            }
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $insertStmt = $this->db->link->prepare("
                INSERT INTO admins (name, email, password) 
                VALUES (?, ?, ?)
            ");
        
            if (!$insertStmt) {
                throw new Exception("Insert prepare failed: " . $this->db->link->error);
            }
        
            $insertStmt->bind_param("sss", $name, $email, $hashedPassword);
        
            if (!$insertStmt->execute()) {
                throw new Exception("Insert execute failed: " . $insertStmt->error);
            }
        
            if ($insertStmt->affected_rows > 0) {
                $insertStmt->close();
                return '<span style="color:green;">Registration Successful</span>';
            } else {
                $insertStmt->close();
                throw new Exception("Registration failed - no rows inserted");
            }
        
        } catch (Exception $e) {
            // Log the error here if needed
            throw $e;
        } catch (\Throwable $th) {
            // Catch any other types of errors
            throw $th;
        }
      
    }
}
?>