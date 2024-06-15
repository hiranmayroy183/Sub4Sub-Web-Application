<?php
// header.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUB4SUB - Get Unlimited Subscriber to your channel ASAP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <div class="container">
    <div class="pg-lib-item-box" data-pgc-field="component">
        <nav class="bg-white navbar navbar-expand-lg navbar-light pg-lib-item py-lg-1 text-uppercase"> <a class="fw-bold navbar-brand text-primary" href="#">SUB4SUB</a> 
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown-5" aria-controls="navbarNavDropdown-5" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span> 
            </button>                         
            <div class="collapse navbar-collapse " id="navbarNavDropdown-5"> 
                <ul class="mb-2 mb-lg-0 ms-auto navbar-nav"> 
                    <li class="nav-item"> <a class="active nav-link px-lg-3 py-lg-4" aria-current="page" href="index.php">Home</a> 
                    </li>                                 
                    <li class="nav-item"> <a class="nav-link px-lg-3 py-lg-4" href="about.php">About</a> 
                    </li>                                 
                    <li class="nav-item"> <a class="nav-link px-lg-3 py-lg-4" href="contact.php">Contact</a> 
                    </li>
                
                    </li>                                 
                    <li class="nav-item"> <a class="fw-bold nav-link px-lg-3 py-lg-4" href="sub4sub.php">SUB4SUB</a> 
                    </li>
                
                
                </ul>                             
                <div class="ms-lg-3">
                    <a class="btn btn-primary pb-2 pe-4 ps-4 pt-2" href="account.php">Account</a>
                </div>                             
            </div>                         
        </nav>
    </div>
</div>
