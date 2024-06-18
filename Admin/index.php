<?php
// adminAbout.php
include '../functions/header.php';
include '../functions/db.php';
include '../functions/auth.php';

if (!isAdminLoggedIn()) {
    header('Location: adminLogin.php');
    exit;
}
else{
    header('Location: adminDashboard.php');
    
}