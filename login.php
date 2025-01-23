<?php 
error_reporting( E_ALL );
ini_set( "display_errors", 1 );
ob_start();
include "lib/session.php";
// session::init();
session::checklogin();
?> 
<?php
 include 'config/config.php';
 include 'lib/database.php';
 include 'helpers/format.php';
 include "class/adminlogin.php";
try {
    $admin=new adminlogin();
} catch (Exception $e) {
    echo "Database Connection Error: " . $e;
    exit();
}

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['submit'])){
    try {
        $adminLogin=$admin->login($_POST);

    } catch (Exception $e) {
        echo "Login Error: " . $e;
        exit();
    }
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Event Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Login</h2>
                        <span class="msg"><?php if(isset($adminLogin)){echo $adminLogin;}?></span>
                        <form method="POST" id="loginForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password">
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        <p class="text-center mt-3">
                            Don't have an account? <a href="register.php">Register here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>