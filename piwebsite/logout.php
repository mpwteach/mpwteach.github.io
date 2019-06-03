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

    <p>Sorry to se you go! <?= $_SESSION["uname"] ?> </p>
    <?php
       // unset a single variable
       unset($_SESSION["uname"]);
     ?>

    <a href="session1.php">back</a>    
    
  </body>
</html>
