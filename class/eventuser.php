
<?php
include_once 'helpers/utils.php';
include_once 'class/event.php';
class eventuser {
    private $db;
    private $fm;
    private $admin_id;
    private $event;
    private $table = 'event_users';

    public function __construct() {
        $this->db = new database();
        $this->fm = new format();
        $this->event = new event();
    }

    public function checkEmail($data,$event_id){
        try {
            if (empty($data["email"])) {
                throw new Exception("Email required",422);
            }
            $email = $this->fm->valid($data['email']);
    
            $checkStmt = $this->db->link->prepare("SELECT email FROM $this->table WHERE email = ? && event_id = ? LIMIT 1");
            if (!$checkStmt) {
                throw new Exception("Email check prepare failed: " . $this->db->link->error);
            }
        
            $checkStmt->bind_param("si", $email,$event_id);
            
            if (!$checkStmt->execute()) {
                throw new Exception("Email check execute failed: " . $checkStmt->error);
            }
        
            $result = $checkStmt->get_result();
            $checkStmt->close();
        
            if ($result->num_rows > 0) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw $e;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function registerEvent($data){
        try {
            if($this->checkEmail($data,$data['event_id'])){
                throw new Exception("Email already registered",422);
            }
            $required_fields = ['event_id','name', 'email', 'mobile'];
            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    return ucfirst(str_replace('_', ' ', $field)) . ' is required';
                }
            }
    
            $name = $this->fm->valid($data['name']);
            $event_id = $this->fm->valid($data['event_id']);
            $email = $this->fm->valid($data['email']);
            $mobile = $this->fm->valid($data['mobile']);
    
            $stmt = $this->db->link->prepare("
                INSERT INTO $this->table (
                    event_id, 
                    name, 
                    email, 
                    mobile
                ) VALUES (?,?, ?, ?)
            ");
        
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->link->error);
            }
        
            $stmt->bind_param("isss", 
                $event_id,
                $name,
                $email,
                $mobile
            );
        
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
        
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return 'Register Successfully';
            } else {
                $stmt->close();
                throw new Exception("No rows were inserted");
            }
        
        } catch (Exception $e) {
            throw $e;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($event_id,$id) {
        try {
            $eventStmt=$this->event->findById($event_id);
            $event = $eventStmt->fetch_assoc();
            if(!$event){
                throw new Exception('Event not found');
            }   
            $stmt = $this->db->link->prepare("DELETE FROM $this->table WHERE id = ? && event_id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->link->error);
            }
            
            $stmt->bind_param("ii", $id,$event_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            if ($stmt->affected_rows > 0) {
                $stmt->close();
                $this->event->decreaseParticipant($event_id, $event["admin_id"]);
                return '<span style="color:green;">User Deleted Successfully</span>';
            } else {
                $stmt->close();
                throw new Exception("Delete failed");
            }
            
        } catch (Exception $e) {
            throw $e;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getPaginatedUsers($event_id,$page = 1, $limit = 10, $search = '', $sortField = 'created_at', $sortOrder = 'DESC') {
        try{
            $offset = ($page - 1) * $limit;
        
            $allowedSortFields = ['name', 'email', 'created_at', 'mobile'];
            $sortField = in_array($sortField, $allowedSortFields) ? $sortField : 'created_at';
            $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
            
            $searchCondition = ' WHERE event_id = ?';
            $searchParams = [$event_id];
            $types = 'i';
            
            if (!empty($search)) {
                $searchCondition .= " AND (name LIKE ? OR email LIKE ? OR mobile LIKE ?)";
                $searchTerm = "%{$search}%";
                $searchParams = array_merge($searchParams, [$searchTerm, $searchTerm, $searchTerm]);
                $types .= 'sss';
            }
            
            $countQuery = "SELECT COUNT(*) as total FROM $this->table" . $searchCondition;
            $countStmt = $this->db->link->prepare($countQuery);
            $countStmt->bind_param($types, ...$searchParams);
            $countStmt->execute();
            $countResult = $countStmt->get_result();
            $countStmt->close();
            $totalRecords = $countResult->fetch_assoc()['total'];
            
            $query = "SELECT * FROM $this->table" . $searchCondition . " ORDER BY {$sortField} {$sortOrder} LIMIT ? OFFSET ?";
            $stmt = $this->db->link->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->link->error);
            }
            
            $allParams = array_merge($searchParams, [$limit, $offset]);
            $types .= 'ii';
            $stmt->bind_param($types, ...$allParams);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $events = [];
            while ($row = $result->fetch_assoc()) {
                $events[] = $row;
            }
            
            $stmt->close();
            
            return [
                'data' => $events,
                'total' => $totalRecords,
                'page' => $page,
                'limit' => $limit,
                'totalPages' => ceil($totalRecords / $limit)
            ];
            
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>
