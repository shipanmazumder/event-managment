<?php include 'inc/header.php'; ?>
<?php include 'inc/sidebar.php'; ?>
<?php include_once "class/event.php"; ?>
<?php
try {
    $event=new event();
} catch (Exception $e) {
    echo "Error: " . $e;
    exit();
}

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['submit'])){
    try {
        $eventCreate=$event->createEvent($_POST);
    } catch (Exception $e) {
        echo "Create Error: " . $e;
        exit();
    }
} 
?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Create New Event</h2>
                        <span class="msg"><?php if(isset($eventCreate)){echo $eventCreate;}?></span>
                        <form method="POST" id="eventForm">
                            <div class="mb-3">
                                <label for="eventName" class="form-label">Event Name</label>
                                <input type="text" name="name" class="form-control" id="eventName" required>
                            </div>
                            <div class="mb-3">
                                <label for="eventDate" class="form-label">Date</label>
                                <input type="date"  name="event_date" class="form-control" id="eventDate" required>
                            </div>
                            <div class="mb-3">
                                <label for="eventTime" class="form-label">Time</label>
                                <input type="time" name="event_time" class="form-control" id="eventTime" required>
                            </div>
                            <div class="mb-3">
                                <label for="eventLocation" class="form-label">Location</label>
                                <input type="text" name="location" class="form-control" id="eventLocation" required>
                            </div>
                            <div class="mb-3">
                                <label for="eventCapacity" class="form-label">Maximum Participant</label>
                                <input type="number" min='1' value="1" name="maximum_participant" class="form-control" id="eventCapacity" required>
                            </div>
                            <div class="mb-3">
                                <label for="eventDescription" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="eventDescription" rows="3" required></textarea>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary w-100">Create Event</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include 'inc/footer.php'; ?>