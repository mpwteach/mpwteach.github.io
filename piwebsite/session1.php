<?php
   session_start();
   // if you use sessions - session_start() must appear first
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Session Example</title>
  </head>


  <body>

    <h1>Session Example</h1>
    <p>Demo of retaining state across multiple page requests using PHP sessions.</p>
    <p>In this example we use the variable <i>hits</i> to record how many times this page
      has been viewed.</p>
    <p>We also use a very simple login mechanism to personalize the site via a <i>uname</i>
      variable</p>
    
    <?php
       // code to increment the hits variable
       if ( !isset($_SESSION["hits"]))
         $_SESSION["hits"]=0;
       $_SESSION["hits"]++;

       // code to get the login name from the form in login.php and set uname variable
       $arg1 = "";

       if ($_SERVER["REQUEST_METHOD"] == "POST") {
         $_SESSION["uname"] = test_input($_POST["arg1"]);
       }

       function test_input($data) {
         $data = trim($data);
         $data = stripslashes($data);
         $data = htmlspecialchars($data);
         return $data;
       }
    ?>

    <h3>Example of accessing the <i>hits</i> variable</h3>
    <p>This page has been viewed <?= $_SESSION["hits"] ?> times in this session. </p>

    <h3>Example of a login screen</h3>
    <?php
       if (!isset($_SESSION["uname"]))
         echo "<a href=login.php>login</a>";
       else
         echo "<p> Welcome back : " . $_SESSION["uname"] . "</p>";
       ?>

    <hr>
    <ul>
      <?php
	 if (isset($_SESSION["uname"]))
           echo "<li><a href=logout.php>logout</a></li>";
	 ?>
      <li><a href="endsession.php">end session</a></li>
      <li><a href="index.html">home</a></li>
    </ul>
  </body>
</html>
