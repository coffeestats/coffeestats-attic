<?php
session_start();
if(session_destroy()) {
    include('../includes/common.php');
    redirect_to('login.php');
}
?>
