<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/connect.php';
require_once 'includes/functions.php';
include 'includes/header.php';
?>
<div class="back_re">
   <div class="container">
      <div class="row">
         <div class="col-md-12">
            <div class="title">
               <h2>gallery</h2>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- gallery -->
<div class="gallery">
   <div class="container">

      <div class="row">
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="images/gallery1.jpg" alt="#" /></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="images/gallery2.jpg" alt="#" /></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="images/gallery3.jpg" alt="#" /></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="images/gallery4.jpg" alt="#" /></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="images/gallery5.jpg" alt="#" /></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="images/gallery6.jpg" alt="#" /></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="images/gallery7.jpg" alt="#" /></figure>
            </div>
         </div>
         <div class="col-md-3 col-sm-6">
            <div class="gallery_img">
               <figure><img src="images/gallery8.jpg" alt="#" /></figure>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- end gallery -->
<?php include 'includes/footer.php'; ?>