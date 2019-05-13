<!DOCTYPE html>
<html>
  <head>
    <title>Form Input 3</title>
  </head>


  <body>

    <h1>Form Input - Demo 3 - w. Radio Buttons</h1>
    <p>Demo of how to take form input and pass it to a program - all in a single page</p>
    <p>This example uses a form with radio buttons</p>
    
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
      <br><br>
      <input type="submit" value="Go!">
    </form>

    <?php
       // only display if return code is numeric - i.e. exec has been called
       if (is_numeric($retc)) {
         echo "<h2>Your Input:</h2>";
         echo "Arg1: " . $arg1;
         echo "<br>";
         echo "Arg2: " . $arg2;
         echo "<br>";
         echo "Arg3: " . $arg3;
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
