<?php
ob_start();
session_start();

header('Content-Type: application/json');
include "lib/session.php"; 
include 'config/config.php';
include 'lib/database.php';
include 'helpers/format.php';
include_once "class/event.php";

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Invalid request method');
    }

    // Get JSON data
    $eventId = $_GET['id'];

    if (!$eventId) {
        throw new Exception('Invalid Id');
    }
    $eventObj = new event();
    $eventStmt = $eventObj->findById($eventId);
    $event = $eventStmt->fetch_assoc();
    
    if (!$event) {
        throw new Exception('Event not found');
    }

    echo json_encode([
        'success' => true,
        'data' => $event
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
