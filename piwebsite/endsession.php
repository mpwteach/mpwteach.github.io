<?php
   // if you use sessions - session_start() must appear first
   session_start();
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Session Example</title>
  </head>


  <body>

    <p>Session ended.</p>
    <?php
       // unset everything
       session_destroy();
     ?>

    <a href="session1.php">back</a>    
    
  </body>
</html>
