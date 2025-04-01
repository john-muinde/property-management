<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

// Get current page
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <!-- basic -->
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <!-- mobile metas -->
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="viewport" content="initial-scale=1, maximum-scale=1">
   <!-- site metas -->
   <title>Lakeside Resorts and Spa</title>
   <meta name="keywords" content="resort, spa, lake, vacation, relax">
   <meta name="description" content="Lakeside Resorts and Spa - Your perfect getaway">
   <meta name="author" content="">
   <!-- bootstrap css -->
   <link rel="stylesheet" href="css/bootstrap.min.css">
   <!-- style css -->
   <link rel="stylesheet" href="css/style.css">
   <!-- Responsive-->
   <link rel="stylesheet" href="css/responsive.css">
   <!-- fevicon - removed problematic reference -->
   <!-- Scrollbar Custom CSS -->
   <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
   <!-- Tweaks for older IEs-->
   <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
   <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>
<!-- body -->

<body class="main-layout">
   <!-- loader  -->
   <div class="loader_bg">
      <div class="loader"><img src="images/loading.gif" alt="#" /></div>
   </div>
   <!-- end loader -->

   <?php
   // Display simple success or error messages if set in session
   if (isset($_SESSION['message'])) {
      $message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'info';
      echo '<div class="container mt-3">';
      echo '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">';
      echo $_SESSION['message'];
      echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
      echo '<span aria-hidden="true">&times;</span></button></div></div>';

      // Clear the message after displaying
      unset($_SESSION['message']);
      unset($_SESSION['message_type']);
   }
   ?>

   <!-- header -->
   <header>
      <!-- header inner -->
      <div class="header">
         <div class="container">
            <div class="row">
               <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col logo_section">
                  <div class="full">
                     <div class="center-desk">
                        <div class="logo">
                           <a href="index.php"><img src="images/logo.svg" alt="#" /></a>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                  <nav class="navigation navbar navbar-expand-md navbar-dark ">
                     <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                     </button>
                     <div class="collapse navbar-collapse" id="navbarsExample04">
                        <ul class="navbar-nav mr-auto">
                           <li class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                              <a class="nav-link" href="index.php">Home</a>
                           </li>
                           <li class="nav-item <?php echo ($current_page == 'rooms.php') ? 'active' : ''; ?>">
                              <a class="nav-link" href="rooms.php">Accommodations</a>
                           </li>
                           <li class="nav-item <?php echo ($current_page == 'spa_services.php') ? 'active' : ''; ?>">
                              <a class="nav-link" href="spa_services.php">Spa Services</a>
                           </li>
                           <li class="nav-item <?php echo ($current_page == 'gallery.php') ? 'active' : ''; ?>">
                              <a class="nav-link" href="gallery.php">Gallery</a>
                           </li>
                           <li class="nav-item <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">
                              <a class="nav-link" href="contact.php">Contact Us</a>
                           </li>
                        </ul>
                     </div>
                  </nav>
               </div>
            </div>
         </div>
      </div>
   </header>
   <!-- end header inner -->
   <!-- end header -->