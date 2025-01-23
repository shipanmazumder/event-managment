<?php
include_once 'config/config.php';
include_once 'lib/database.php';
include_once 'helpers/format.php';
include_once 'class/eventuser.php';
include_once 'class/event.php';

try {
    // Initialize objects without session dependency
    $db = new database();
    $fm = new format();
    $eventObj = new event();
    $eventUserObj = new eventuser();
    
    // Get event details from query parameter
    $event_id = isset($_GET['id']) ? $_GET['id'] : null;
    if (!$event_id) {
        header("Location: dashboard.php");
        exit();
    }
    
    $event = $eventObj->getEventById($event_id);
    if (!$event) {
        header("Location: dashboard.php");
        exit();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register for Event - Event Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Event Registration</h2>
                        <div id="message" class="alert" style="display: none;"></div>
                        <form id="eventRegistrationForm" novalidate>
                            <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event_id); ?>">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="invalid-feedback">Please enter your full name</div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback">Please enter a valid email address</div>
                            </div>

                            <div class="mb-3">
                                <label for="mobile" class="form-label">Mobile Number</label>
                                <input type="tel" class="form-control" id="mobile" name="mobile" required>
                                <div class="invalid-feedback">Please enter your mobile number</div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Register for Event</button>
                        </form>
                        <p class="text-center mt-3">
                            <a href="index.php">Back to Events</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/event-registration.js"></script>
</body>
</html>
