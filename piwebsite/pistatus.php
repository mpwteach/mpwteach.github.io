<!DOCTYPE html>
<html>
  <head>
    <title>Pi Status</title>
  </head>


  <body>

    <h1>Pi Status</h1>
    <p>Check the status of class Pi's</p>

    <?php
       // define variables and set to empty values
       $arg1 = $output = $retc = "";

       if ($_SERVER["REQUEST_METHOD"] == "POST") {
         $arg1 = test_input($_POST["arg1"]);
         exec("/usr/lib/cgi-bin/pi/rpisalive " . $arg1, $output, $retc); 
       }

       function test_input($data) {
         $data = trim($data);
         $data = stripslashes($data);
         $data = htmlspecialchars($data);
         return $data;
       }
    ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      Pi's to test: <input type="text" name="arg1"><br>
      <br>
      <input type="submit" value="Check now!">
    </form>

    <hr>
  
    <?php
       if (is_numeric($retc)) {
         echo "<h2>Your Input:</h2>";
         echo $arg1;
         echo "<br>";
        
         echo "<h2>Program Output (an array):</h2>";
         foreach ($output as $line) {
           echo $line;
           echo "<br>";
         }
       
         echo "<h2>Program Return Code:</h2>";
         echo $retc;
       }
    ?>
    
  </body>
</html>
