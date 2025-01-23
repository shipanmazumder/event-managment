<?php
include_once 'config/config.php';
include 'lib/session.php';
Session::checkSession();

include_once 'lib/database.php';
include_once 'helpers/format.php';
include_once 'class/eventuser.php';
include_once 'class/event.php';

if (headers_sent()) {
    die('Headers already sent');
}
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="events.csv"');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    throw new Exception('Invalid request method');
}

// Get JSON data
$eventId = $_GET['event_id'];

$output = fopen('php://output', 'w');
if ($output === false) {
    die('Failed to create output stream');
}

fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

try {
    $eventName='Test Event';
    $eventObj=new event();
    $event=$eventObj->getEventById($eventId);
    if($event){
        $eventName=$event['name'];
        $eventDate=$event['event_date'];
        $eventTime=$event['event_time'];
        $location=$event['location'];
        $totalRegister=$event['total_participate'];
        fputcsv($output, ['Event Name: '.$eventName, 'Date: '.$eventDate, 'Time: '.$eventTime, 'Location: '.$location, 'Total Register: '.$totalRegister]);
    }

    fputcsv($output, ['Name', 'Email', 'Mobile']);
    $eventUserObj = new eventuser();
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $sortField = isset($_GET['sort']) ? $_GET['sort'] : 'event_date';
    $sortOrder = isset($_GET['order']) ? $_GET['order'] : 'DESC';
    $events = $eventUserObj->getPaginatedUsers($eventId,1, PHP_INT_MAX, $search, $sortField, $sortOrder);
    if (!empty($events['data'])) {
        foreach ($events['data'] as $event) {
            fputcsv($output, [
                $event['name'],
                $event['email'],
                $event['mobile']
            ]);
        }
    }
    fclose($output);
    exit();
} catch (Exception $e) {
    if ($output) {
        fclose($output);
    }
    die('Error exporting events: ' . $e->getMessage());
}
?>
