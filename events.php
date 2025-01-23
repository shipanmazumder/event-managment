<?php

error_reporting( E_ALL );
ini_set( "display_errors", 1 );
?>

<?php include "config/config.php"; ?>
<?php include "lib/database.php"; ?>
<?php include "helpers/format.php"; ?>
<?php 
include 'class/event.php';
try {
    $eventObj = new event();
    $events = $eventObj->getActiveEvents();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>
<?php
  header("Cache-Control: no-cache, must-revalidate"); 
  header("Pragma: no-cache"); 
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); 
  header("Cache-Control: max-age=2592000"); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo TITLE;  ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center mb-4">Upcoming Events</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $event): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($event['name']); ?></h5>
                            <p class="card-text">
                                <i class="bi bi-calendar"></i> 
                                <?php echo date('d M Y', strtotime($event['event_date'])); ?>
                            </p>
                            <p class="card-text">
                                <i class="bi bi-clock"></i> 
                                <?php echo date('h:i A', strtotime($event['event_time'])); ?>
                            </p>
                            <p class="card-text">
                                <i class="bi bi-geo-alt"></i> 
                                <?php echo htmlspecialchars($event['location']); ?>
                            </p>
                            <p class="card-text">
                                <i class="bi bi-people"></i> 
                                Remaining: <?php echo htmlspecialchars($event['maximum_participant']-$event['total_participate']); ?>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <?php
                            $spotsLeft = $event['maximum_participant'] - $event['total_participate'];
                            $buttonClass = $spotsLeft > 0 ? 'btn-primary' : 'btn-secondary disabled';
                            ?>
                            <a href="register-event.php?id=<?php echo $event['id']; ?>" 
                               class="btn <?php echo $buttonClass; ?> w-100">
                                <?php echo $spotsLeft > 0 ? 'Register Now' : 'Full'; ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    No upcoming events available at the moment.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
