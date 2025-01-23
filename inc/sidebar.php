
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Event Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $fm->title()=="Dashboard"?"active":"" ?>" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $fm->title()=="Create Event"?"active":"" ?>" href="create-event.php">Create Event</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                         <?php
                           if(isset($_GET["action"]) && $_GET["action"]=='logout' ){
                              session::destory();
                           }
                           ?>
                        <a class="nav-link" href="?action=logout" > Logout</a>
                    </li>
                </ul>
            </div>
        </div>
</nav>