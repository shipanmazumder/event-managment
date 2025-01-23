<?php
header('Content-Type: application/json');
include 'config/config.php';
include 'lib/database.php';
include 'helpers/format.php';
include_once "class/eventuser.php";
include_once "class/event.php";

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get JSON data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    // Validate event exists and is active
    $eventObj = new event();
    $event = $eventObj->getEventById($data['event_id']);
    
    if (!$event) {
        throw new Exception('Event not found or inactive');
    }
    if($event['status'] == 0){
        throw new Exception('Event is inactive');
    }
    if($event['maximum_participant'] <= $event['total_participate']){
        throw new Exception('Event is full ');
    }

    // Register for event
    $eventUserObj = new eventuser();
    $result = $eventUserObj->registerEvent($data);

    $eventObj->increaseParticipant($data['event_id'], $event["admin_id"]);

    echo json_encode([
        'success' => true,
        'message' => $result
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
