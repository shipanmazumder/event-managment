<?php

error_reporting( E_ALL );
ini_set( "display_errors", 1 );
?>
<?php include "config/config.php"; ?>
<?php include "lib/database.php"; ?>
<?php include "helpers/format.php"; ?>
<?php
ob_start();
include "lib/session.php"; 
session::checksession();
$fm=new format();
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
    <title><?php echo TITLE;  ?> | <?php echo $fm->title();  ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>