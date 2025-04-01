<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/connect.php';
require_once 'includes/functions.php';
include 'includes/header.php';
?>
<style>
   ::-webkit-calendar-picker-indicator {
      filter: invert(1);
   }
</style>

<!-- banner -->
<section class="banner_main">
   <div id="myCarousel" class="carousel slide banner" data-ride="carousel">
      <ol class="carousel-indicators">
         <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
         <li data-target="#myCarousel" data-slide-to="1"></li>
         <li data-target="#myCarousel" data-slide-to="2"></li>
      </ol>
      <div class="carousel-inner">
         <div class="carousel-item active">
            <img class="first-slide" src="images/banner1.jpg" alt="First slide">
            <div class="container">
               <div class="carousel-caption">
                  <h1>Welcome to Lakeside Resort & Spa</h1>
                  <p>Discover peace and luxury by the water</p>
               </div>
            </div>
         </div>
         <div class="carousel-item">
            <img class="second-slide" src="images/banner2.jpg" alt="Second slide">
            <div class="container">
               <div class="carousel-caption">
                  <h1>Relaxation & Rejuvenation</h1>
                  <p>Award-winning spa treatments</p>
               </div>
            </div>
         </div>
         <div class="carousel-item">
            <img class="third-slide" src="images/banner3.jpg" alt="Third slide">
            <div class="container">
               <div class="carousel-caption">
                  <h1>Lakefront Accommodations</h1>
                  <p>Wake up to breathtaking views</p>
               </div>
            </div>
         </div>
      </div>
      <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
         <span class="carousel-control-prev-icon" aria-hidden="true"></span>
         <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
         <span class="carousel-control-next-icon" aria-hidden="true"></span>
         <span class="sr-only">Next</span>
      </a>
   </div>
   <div class="booking_ocline">
      <div class="container">
         <div class="row">
            <div class="col-md-5">
               <div class="book_room">
                  <h1>Book a Room Online</h1>
                  <form class="book_now" action="api/check_availability.php" method="post">
                     <input type="hidden" name="token" value="<?php echo generate_token(); ?>">
                     <div class="row">
                        <div class="col-md-12">
                           <span>Arrival</span>
                           <input class="online_book" type="date" name="arrival_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-12">
                           <span>Departure</span>
                           <input class="online_book" type="date" name="departure_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                        </div>
                        <div class="col-md-12">
                           <button class="book_btn">Check Availability</button>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<!-- end banner -->

<!-- about -->
<div class="about">
   <div class="container-fluid">
      <div class="row">
         <div class="col-md-5">
            <div class="titlepage">
               <h2>About Lakeside Resort</h2>
               <p>Nestled on the tranquil shores of a pristine lake, Lakeside Resort and Spa offers luxury accommodations, world-class spa treatments, and breathtaking views. Whether you're planning a romantic getaway, a family vacation, or a wellness retreat, our resort provides the perfect setting for relaxation and rejuvenation.</p>
               <a class="read_more" href="rooms.php">Explore Our Rooms</a>
            </div>
         </div>
         <div class="col-md-7">
            <div class="about_img">
               <figure><img src="images/about.png" alt="#" /></figure>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- end about -->

<!-- our_room -->
<div class="our_room">
   <div class="container">
      <div class="row">
         <div class="col-md-12">
            <div class="titlepage">
               <h2>Our Rooms</h2>
               <p>Comfort and luxury in every detail</p>
            </div>
         </div>
      </div>
      <div class="row">
         <?php
         // Get featured rooms
         $rooms = get_rooms();
         $count = 0;

         foreach ($rooms as $room) {
            if ($count >= 3) break; // Show only 3 rooms on homepage
         ?>
            <div class="col-md-4 col-sm-6">
               <div id="serv_hover" class="room">
                  <div class="room_img">
                     <figure><img src="images/<?php echo htmlspecialchars($room['image']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" /></figure>
                  </div>
                  <div class="bed_room">
                     <h3><?php echo htmlspecialchars($room['name']); ?></h3>
                     <p><?php echo htmlspecialchars(substr($room['description'], 0, 100)) . '...'; ?></p>
                     <p><strong>From <?php echo format_price($room['price']); ?>/night</strong></p>
                     <a href="rooms.php" class="read_more">View Details</a>
                  </div>
               </div>
            </div>
         <?php
            $count++;
         }
         ?>
      </div>
   </div>
</div>
<!-- end our_room -->

<!-- gallery -->
<div class="gallery">
   <div class="container">
      <div class="row">
         <div class="col-md-12">
            <div class="titlepage">
               <h2>Gallery</h2>
            </div>
         </div>
      </div>
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
      </div>
      <div class="row">
         <div class="col-md-12">
            <a class="read_more" href="gallery.php">See More</a>
         </div>
      </div>
   </div>
</div>
<!-- end gallery -->

<?php
include 'includes/footer.php';
?>