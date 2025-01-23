<?php 
ob_start();
include "lib/session.php";
?> 
<?php
 session::init();
 include 'config/config.php';
 include 'lib/database.php';
 include 'helpers/format.php';
 include_once "class/adminlogin.php";
try {
    $admin=new adminlogin();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['submit'])){
    try {
        $adminReg=$admin->register($_POST);

    } catch (Exception $e) {
        echo "Registration Error: " . $e->getMessage();
        exit();
    }
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Event Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Register</h2>
                        <span class="msg"><?php if(isset($adminReg)){echo $adminReg;}?></span>
                        <form method="POST" id="registerForm">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password">
                            </div>
                            <div class="mb-3">
                                <label for="confirm-password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm-password" name="confirm-password" required autocomplete="new-password">
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary w-100">Register</button>
                        </form>
                        <p class="text-center mt-3">
                            Already have an account? <a href="login.php">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/auth.js"></script>
</body>
</html>
