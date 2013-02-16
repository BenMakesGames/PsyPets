<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Hours</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; Hours</h4>
     <p>Every hour that passes in real life gives you an hour of time to spend on your pets.  Each hour spent, your pets may hunt, make a vase, gather food, or perform any of a number of activities (<a href="/help/petcare.php">which you can read more about</a>).  They also become more hungry and tired with each passing hour.</p>
     <p>These hours do not pass until you give the OK, giving you time to move items around, feed your pets, and do other housekeeping before letting them loose to do their stuff.</p>
     <p>If you've been gone for a very long time and worry about your pets' health (for example, that they may starve), you may choose to skip the hours.</p>
     <h5>Allowance</h5>
     <p>Every 24 hours of pet activity, you will collect allowance.</p>
     <p>You can see how many more hours you must pass at the top of every page.  ("You will collect allowance in X hours hours.")</p>
     <p>Skipped hours do not count toward allowance.</p>
     <p>There are several options for what you receive as allowance.  To change what you receive, <a href="/allowance.php">visit The Bank</a>.</p>
     <h5>Automatically Using Hours</h5>
     <p>You may set a number of hours for the game to pass automatically.  If you are on very frequently, for example, you might want the game to automatically spend your hours if you only have 1 of them.</p>
     <p>By default, no hours will be passed automatically.  You can change this setting from the <a href="/myaccount/behavior.php">My Account > Behavior Settings</a> page.</p>
     <h5>Pet Order</h5>
     <p>The order your pets are displayed in your house is the order that they take action during each hour.</p>
     <p>For example, if you had two pets, Alain and Roy, with Alain listed first, and 2 hours took place, then the order they act would be:</p>
     <ul>
      <li>Hour 1:<ol>
       <li>Alain</li>
       <li>Roy</li>
      </ol></li>
      <li>Hour 2:<ol>
       <li>Alain</li>
       <li>Roy</li>
      </ol></li>
     </ul>
     <p>If those two pets commonly used the same materials, and you wanted Roy to use those materials more than Alain, then you would want to rearrange the pets such that Roy is listed before Alain.</p>
     <p>You can change how your pets are arranged from the <a href="/myhouse/arrange_pets.php">Rearranged Pets page</a>.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
