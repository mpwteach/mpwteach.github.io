<!DOCTYPE html>
<html>
  <head>
    <title>Form Input 4</title>
  </head>


  <body>

    <h1>Form Input - w. Radio Buttons</h1>
    <p>Demo of how to take form input and pass it to a C program - all in a single page</p>

    <?php
       // define variables and set to empty values
       $arg1 = $arg2 = $arg3 = $output = $retc = "";

       if ($_SERVER["REQUEST_METHOD"] == "POST") {
         $arg1 = test_input($_POST["arg1"]);
         $arg2 = test_input($_POST["arg2"]);
         $arg3 = test_input($_POST["arg3"]);
         exec("/usr/lib/cgi-bin/pi/argtest2 " . $arg1 . " " . $arg2, $output, $retc); 
       }

       function test_input($data) {
         $data = trim($data);
         $data = stripslashes($data);
         $data = htmlspecialchars($data);
         return $data;
       }
    ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      Arg1: <input type="text" name="arg1"><br>
      Arg2: <input type="text" name="arg2"><br>
      Arg3:
      <input type="radio" name="arg3" value="1">b1
      <input type="radio" name="arg3" value="2">b2
      <input type="radio" name="arg3" value="3">b3
      <br>
      <input type="submit">
    </form>

    <?php
       echo "<h2>Your Input:</h2>";
       echo "Arg1: " . $arg1;
       echo "<br>";
       echo "Arg2: " . $arg2;
       echo "<br>";
       echo "Arg3: " . $arg3;
       echo "<br>";
       
       echo "<h2>C Program Output (an array):</h2>";
       foreach ($output as $line) {
         echo $line;
         echo "<br>";
       }
       
       echo "<h2>C Program Return Code:</h2>";
       echo $retc;
      
     ?>
    
  </body>
</html>
