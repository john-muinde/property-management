<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/connect.php';
require_once 'includes/functions.php';
include 'includes/header.php';
if (!isset($_SESSION['csrf_token'])) {
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<div class="back_re">
   <div class="container">
      <div class="row">
         <div class="col-md-12">
            <div class="title">
               <h2>Contact Us</h2>
            </div>
         </div>
      </div>
   </div>
</div>
<!--  contact -->
<div class="contact">
   <div class="container">
      <div class="row">
         <div class="col-md-6">
            <form id="request" class="main_form" method="post" action="api/contact_submit.php">
               <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
               <div class="row">
                  <div class="col-md-12">
                     <input class="contactus" placeholder="Name" type="text" name="name" required>
                  </div>
                  <div class="col-md-12">
                     <input class="contactus" placeholder="Email" type="email" name="email" required>
                  </div>
                  <div class="col-md-12">
                     <input class="contactus" placeholder="Phone Number" type="text" name="phone">
                  </div>
                  <div class="col-md-12">
                     <textarea class="textarea" placeholder="Message" name="message" required></textarea>
                  </div>
                  <div class="col-md-12">
                     <button class="send_btn" type="submit">Send</button>
                  </div>
               </div>
            </form>
         </div>
         <div class="col-md-6">
            <div class="map_main">
               <div class="map-responsive">
                  <iframe
                     src="https://www.google.com/maps/embed/v1/place?key=AIzaSyA0s1a7phLN0iaD6-UE7m4qP-z21pH0eSc&amp;q=Eiffel+Tower+Paris+France"
                     width="600" height="400" frameborder="0" style="border:0; width: 100%;"
                     allowfullscreen=""></iframe>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- end contact -->
<?php include 'includes/footer.php'; ?>