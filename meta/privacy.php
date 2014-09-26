<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = "no";
$reading_tos = true;

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Privacy Policy</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Privacy Policy</h4>
     <h5>Your E-mail Address</h5>
     <p>An e-mail address is required to sign up for PsyPets.  This e-mail address is not revealed to other players, to other companies, or to anyone else.</p>
     <p>Upon sign up you will be sent a confirmation e-mail to your address.  You will receive no further e-mails unless there is an issue with your account that you need to be contacted about, or because you request to have an e-mail sent to you (for example for password retrieval or forwarding PsyMail messages to your e-mail address).</p>
     <h5>Your Profile, The Plaza (in-game Forum) and PsyMail (in-game mailing system)</h5>
     <p>Some people choose to put personally identifiable information in their profile, including pictures, their state of residence, instant messaging screen names, and more.  This information could also be PsyMailed to other players or posted in the Plaza.</p>
     <p>I do not collect this information or give it to any other party, however I cannot be held responsible for what other PsyPets players do with information which you make available in this way.</p>
     <h5>The Graphics Library</h5>
     <p>When uploading graphics to the graphics library you are asked to provide the name of the artist (probably you).  This name is displayed on the <a href="/meta/copyright.php">Copyright Information</a> page, which is accessible even to non-players.  If you are not comfortable having your name displayed in this way, feel free to use an alias.  Many players use their in-game "resident" name.</p>
     <ul><li>See also: <a href="/meta/copyright.php">Copyright Information</a></li></ul>
     <h5>Payments Made With PayPal</h5>
     <p>Payments made to PsyPets via PayPal include your full name and e-mail address (as you have given it to PayPal).  This information is recorded and associated with the PsyPets account receiving the Favor for the payment.</p>
     <p>A player may, at any time, review their history of payments, along with the information provided by PayPal.  (One more reason to keep your login name and password to yourself!)</p>
     <ul><li>See also: <a href="/wherethemoneygoes.php">What Is "Favor"?</a></li></ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
