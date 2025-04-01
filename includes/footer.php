<!--  footer -->
<footer>
   <div class="footer">
      <div class="container">
         <div class="row">
            <div class=" col-md-4">
               <h3>Contact Us</h3>
               <ul class="conta">
                  <li><i class="fa fa-map-marker" aria-hidden="true"></i> Lakeside Drive, Naivasha, Kenya</li>
                  <li><i class="fa fa-mobile" aria-hidden="true"></i> +254 722 123 456</li>
                  <li><i class="fa fa-envelope" aria-hidden="true"></i><a href="mailto:info@lakesideresorts.co.ke"> info@lakesideresorts.co.ke</a></li>
               </ul>
            </div>
            <div class="col-md-4">
               <h3>Quick Links</h3>
               <ul class="link_menu">
                  <li class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>"><a href="index.php">Home</a></li>
                  <li class="<?php echo ($current_page == 'rooms.php') ? 'active' : ''; ?>"><a href="rooms.php">Accommodations</a></li>
                  <li class="<?php echo ($current_page == 'spa_services.php') ? 'active' : ''; ?>"><a href="spa_services.php">Spa Services</a></li>
                  <li class="<?php echo ($current_page == 'gallery.php') ? 'active' : ''; ?>"><a href="gallery.php">Gallery</a></li>
                  <li class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>"><a href="contact.php">Contact Us</a></li>
               </ul>
            </div>
            <div class="col-md-4">
               <h3>Newsletter</h3>
               <form class="bottom_form" action="api/subscribe.php" method="post">
                  <input type="hidden" name="token" value="<?php echo generate_token(); ?>">
                  <input class="enter" placeholder="Enter your email" type="email" name="email" required>
                  <button class="sub_btn">Subscribe</button>
               </form>
               <ul class="social_icon">
                  <li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                  <li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                  <li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                  <li><a href="#"><i class="fa fa-youtube-play" aria-hidden="true"></i></a></li>
               </ul>
            </div>
         </div>
      </div>
      <div class="copyright">
         <div class="container">
            <div class="row">
               <div class="col-md-10 offset-md-1">
                  <p>
                     © <?php echo date('Y'); ?> All Rights Reserved. Lakeside Resorts and Spa, Naivasha, Kenya
                  </p>
               </div>
            </div>
         </div>
      </div>
   </div>
</footer>
<!-- end footer -->

<!-- Javascript files-->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>

<!-- Skip the problematic plugins and just use our minimal custom JS -->
<script src="js/custom.js"></script>
</body>

</html>