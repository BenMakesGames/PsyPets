<?php
require_once 'commons/init.php';

$require_login = 'no';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Virtual Hide-and-Go-Seek Tag</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Virtual Hide-and-Go-Seek Tag</h4>
     <h5>What it Accomplishes</h5>
     <p>A pet that participates in Virtual Hide-and-Go-Seek Tag competes with other pets, earning pixels when successful.</p>
     <p>Pixels are the only reward for pets that play, however you may trade pixels for many items from the Fiberoptic Link house add-on.</p>
     <h5>What it Requires</h5>
     <p>To play Virtual Hide-and-Go-Seek Tag, a pet must be able to think quickly, move quickly, and above all, be sneaky.</p>
     <p>You <em>must</em> have a Fiberoptic Link house add-on for pets to be able to play.</p>
     <h5>Recommended Equipment</h5>
     <p>Virtual Hide-and-Go-Seek Tag relies on a strange combination of skills and attributes that are not easy to find in any single equipment.</p>
     <p>Some rules of thumb:</p>
     <ul>
      <li>Wings and winged items are generally good.</li>
      <li>Dark-colored or evil-looking things may be good, especially if magical.</li>
      <li>Lanterns and flashlights are bad - such items are bright, making the pet stand out.</li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
