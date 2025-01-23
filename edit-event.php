<?php include 'inc/header.php'; ?>
<?php include 'inc/sidebar.php'; ?>
<?php include_once "class/event.php"; ?>
<?php
try {
    $eventId=$_GET['id'];
    if(!isset($_GET['id'])|| $_GET['id']==null){
        header("Location:dashboard.php");
    }
    $event=new event();
} catch (Exception $e) {
    echo "Error: " . $e;
    exit();
}

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['submit'])){
    try {
        $eventUpdate=$event->update($_POST,$eventId);

        header("Location:dashboard.php");

        // utils::debug_v($eventUpdate);

    } catch (Exception $e) {
        echo "Update Error: " . $e;
        exit();
    }
} 
?>
<?php
   
    if($_SERVER['REQUEST_METHOD']=='POST'){
        $category=$_POST['category'];
        $cattype=$_POST['cattype'];
        $updatecat=$cat->updatecat($category,$cattype,$catid);
    } 
?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Update Event</h2>
                        <span class="msg"><?php if(isset($eventUpdate)){echo $eventUpdate;}?></span>
                        <?php 
                $getEvent=$event->findById($eventId);
                if($getEvent){
                    while($result=$getEvent->fetch_assoc()){
                        ?>
                        <form method="POST" id="eventForm">
                            <div class="mb-3">
                                <label for="eventName" class="form-label">Event Name</label>
                                <input type="text" value="<?php echo $result['name']; ?>" name="name" class="form-control" id="eventName" required>
                            </div>
                            <div class="mb-3">
                                <label for="eventDate" class="form-label">Date</label>
                                <input type="date" value="<?php echo $result['event_date']; ?>"  name="event_date" class="form-control" id="eventDate" required>
                            </div>
                            <div class="mb-3">
                                <label for="eventTime" class="form-label">Time</label>
                                <input type="time" value="<?php echo $result['event_time']; ?>" name="event_time" class="form-control" id="eventTime" required>
                            </div>
                            <div class="mb-3">
                                <label for="eventLocation" class="form-label">Location</label>
                                <input type="text" value="<?php echo $result['location']; ?>" name="location" class="form-control" id="eventLocation" required>
                            </div>
                            <div class="mb-3">
                                <label for="eventCapacity" class="form-label">Maximum Participant</label>
                                <input type="number" min='1' value="<?php echo $result['maximum_participant']; ?>" name="maximum_participant" class="form-control" id="eventCapacity" required>
                            </div>
                            <div class="mb-3">
                                <label for="eventDescription" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="eventDescription" rows="3" required><?php echo $result['description']; ?></textarea>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary w-100">Update Event</button>
                        </form>
                        <?php
                    }
                }
                
            ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include 'inc/footer.php'; ?>