<?php
include_once 'config/config.php';
include 'lib/session.php';
Session::checkSession();

include_once 'lib/database.php';
include_once 'helpers/format.php';
include_once 'class/event.php';

if (headers_sent()) {
    die('Headers already sent');
}
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="events.csv"');

$output = fopen('php://output', 'w');
if ($output === false) {
    die('Failed to create output stream');
}

fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['Event Name', 'Date', 'Time', 'Location', 'Description', 'Maximum Participants']);

try {
    $eventObj = new event();
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $sortField = isset($_GET['sort']) ? $_GET['sort'] : 'event_date';
    $sortOrder = isset($_GET['order']) ? $_GET['order'] : 'DESC';
    $events = $eventObj->getPaginatedEvents(1, PHP_INT_MAX, $search, $sortField, $sortOrder);
    if (!empty($events['data'])) {
        foreach ($events['data'] as $event) {
            fputcsv($output, [
                $event['name'],
                date('d M Y', strtotime($event['event_date'])),
                date('h:i A', strtotime($event['event_time'])),
                $event['location'],
                $event['description'],
                $event['maximum_participant']
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
