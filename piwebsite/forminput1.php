<!DOCTYPE html>
<html>

  <body>

    <h1>Arg Test in PHP</h1>

    <h2>Args entered:</h2>
    Arg1: <?php echo $_POST["arg1"]; ?><br>
    Arg2: <?php echo $_POST["arg2"]; ?>

    <br>

    <h2>Calling C program with the args</h2>
    <?php
       system( "/usr/lib/cgi-bin/pi/argtest2 " . $_POST["arg1"] . " " . $_POST["arg2"]); 
    ?>
    
  </body>
</html>
