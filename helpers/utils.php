<?php
include_once 'lib/session.php';
class utils
{
    public function __construct() {
        session::init();
    }

    public static function is_logged_in()
    {
        if (isset($_SESSION['adminlogin'])) {
            return $_SESSION['adminlogin'];
        } else {
            return false;
        }
    }
    public static function logged_in_user_id()
    {
        if (isset($_SESSION['id'])) {
            return $_SESSION['id'];
        } else {
            return 0;
        }
    }
    public static function logged_in_name()
    {
        $logged_in_name = '';
        if (isset($_SESSION['name'])) {
            $logged_in_name = $_SESSION['name'];
        }
        return $logged_in_name;
    }
    public static function logged_in_email()
    {
        $logged_in_email = '';
        if (isset($_SESSION['email'])) {
            $logged_in_email = $_SESSION['email'];
        }
        return $logged_in_email;
    }
    public static function debug_v($value)
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
        die();
    }
}
