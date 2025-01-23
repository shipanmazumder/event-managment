
<?php
include_once 'helpers/utils.php';
class event {
    private $db;
    private $fm;
    private $admin_id;
    private $table = 'events';

    public function __construct() {
        $this->db = new database();
        $this->fm = new format();
        $this->admin_id=utils::logged_in_user_id();
    }

    public function createEvent($data){
        try {
            $required_fields = ['name', 'event_date', 'event_time', 'description', 'location', 'maximum_participant'];
            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    return ucfirst(str_replace('_', ' ', $field)) . ' is required';
                }
            }
    
            $name = $this->fm->valid($data['name']);
            $date = $this->fm->valid($data['event_date']);
            $time = $this->fm->valid($data['event_time']);
            $description = $this->fm->valid($data['description']);
            $location = $this->fm->valid($data['location']);
            $maximum_participant = $this->fm->valid($data['maximum_participant']);

            
    
            $date = date('Y-m-d', strtotime($date));
    
            $stmt = $this->db->link->prepare("
                INSERT INTO $this->table (
                    admin_id, 
                    name, 
                    event_date, 
                    event_time, 
                    description, 
                    location, 
                    maximum_participant
                ) VALUES (?,?, ?, ?, ?, ?, ?)
            ");
        
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->link->error);
            }
        
            $stmt->bind_param("sssssss", 
                $this->admin_id,
                $name,
                $date,
                $time,
                $description,
                $location,
                $maximum_participant
            );
        
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
        
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return '<span style="color:green;">Event Created Successfully</span>';
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
      
    public function update($data, $id) {
        $required_fields = ['name', 'event_date', 'event_time', 'description', 'location', 'maximum_participant'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                return ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        $name = $this->fm->valid($data['name']);
        $date = $this->fm->valid($data['event_date']);
        $time = $this->fm->valid($data['event_time']);
        $description = $this->fm->valid($data['description']);
        $location = $this->fm->valid($data['location']);
        $maximum_participant = $this->fm->valid($data['maximum_participant']);

        try {
            $stmt = $this->db->link->prepare("
                UPDATE $this->table 
                SET name = ?, 
                    event_date = ?, 
                    event_time = ?, 
                    description = ?, 
                    location = ?, 
                    maximum_participant = ?
                WHERE id = ? && admin_id = ?
            ");

            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->link->error);
            }


            $stmt->bind_param("ssssssii", 
                $name,
                $date,
                $time,
                $description,
                $location,
                $maximum_participant,
                $id,
                $this->admin_id
            );

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            if ($stmt->affected_rows >= 0) {
                $stmt->close();
                return '<span style="color:green;">Event Updated Successfully</span>';
            } else {
                $stmt->close();
                throw new Exception("Update failed");
            }

        } catch (Exception $e) {
            throw $e;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function toggle($id) {
      try{
            $stmt = $this->findById($id);
            $event = $stmt->fetch_assoc();
            $status = $event['status'] == 1 ? 0 : 1;
            $stmt = $this->db->link->prepare("
                UPDATE $this->table 
                SET status = ?
                WHERE id = ? && admin_id = ?
            ");

            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->link->error);
            }

            $stmt->bind_param("iii", 
                $status,
                $id,
                $this->admin_id
            );

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            if ($stmt->affected_rows >= 0) {
                $stmt->close();
                return '<span style="color:green;">Event Status Changed Successfully</span>';
            } else {
                $stmt->close();
                throw new Exception("Update failed");
            }
           

           
        } catch (Exception $e) {
            throw $e;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function findById($id) {
        try {
            $stmt = $this->db->link->prepare("SELECT * FROM $this->table WHERE id = ? AND admin_id=? LIMIT 1");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->link->error);
            }
            
            $stmt->bind_param("ii", $id,$this->admin_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $stmt->close();
                throw new Exception("No event found with ID: " . $id);
            }
            $stmt->close();
            
            return $result;
            
        } catch (Exception $e) {
            throw $e;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->link->prepare("DELETE FROM $this->table WHERE id = ? && admin_id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->link->error);
            }
            
            $stmt->bind_param("ii", $id,$this->admin_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return '<span style="color:green;">Event Deleted Successfully</span>';
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


    public function findAll(){
        $query="SELECT * FROM $this->table ORDER BY updated_at DESC";
        $result=$this->db->select($query);
        return $result;
    }

    public function getActiveEvents() {
        try {
            $stmt = $this->db->link->prepare("SELECT id, name, event_date, event_time, location,maximum_participant,total_participate FROM $this->table WHERE status = 1 ORDER BY event_date ASC");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->link->error);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $events = [];
            while ($row = $result->fetch_assoc()) {
                $events[] = $row;
            }
            
            $stmt->close();
            return $events;
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getEventById($id) {
        try {
            $stmt = $this->db->link->prepare("SELECT * FROM $this->table WHERE id = ? AND status = 1 LIMIT 1");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->link->error);
            }
            
            $stmt->bind_param("i", $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $stmt->close();
                return null;
            }

            $event = $result->fetch_assoc();
            $stmt->close();
            
            return $event;
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getPaginatedEvents($page = 1, $limit = 10, $search = '', $sortField = 'event_date', $sortOrder = 'DESC') {
        try{
            $offset = ($page - 1) * $limit;
        
            $allowedSortFields = ['name', 'event_date', 'event_time', 'location', 'description',"maximum_participant"];
            $sortField = in_array($sortField, $allowedSortFields) ? $sortField : 'event_date';
            $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
            
            $searchCondition = ' WHERE admin_id = ?';
            $searchParams = [$this->admin_id];
            $types = 'i';
            
            if (!empty($search)) {
                $searchCondition .= " AND (name LIKE ? OR location LIKE ? OR description LIKE ?)";
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

    public function increaseParticipant($id,$admin_id)
    {
        try {
            $stmt = $this->db->link->prepare("UPDATE $this->table SET total_participate = total_participate + 1 WHERE id = ? AND admin_id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->link->error);
            }
            
            $stmt->bind_param("ii", $id, $admin_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return true;
            } else {
                $stmt->close();
                throw new Exception("Update failed");
            }
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function decreaseParticipant($id,$admin_id)
    {
        try {
            $stmt = $this->db->link->prepare("UPDATE $this->table SET total_participate = total_participate - 1 WHERE id = ? AND admin_id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->link->error);
            }
            
            $stmt->bind_param("ii", $id, $admin_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return true;
            } else {
                $stmt->close();
                throw new Exception("Update failed");
            }
            
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>
