<?php
require_once 'commons/init.php';

$require_login = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Pet Pregnancy</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Pet Pregnancy</h4>
     <h5>How Pets Become Pregnant (Or "When Two PsyPets Love Each Other Very Much")</h5>
		 <p>Yes, in <?= $SETTINGS['site_name'] ?> your pets can have sex.  And when an unfixed male and unfixed female have sex, it's possible that the female becomes pregnant.</p>
		 <p>When two pets with a strong and loving relationship hang out, there's a strong possibility that they have sex.  When they do, a heart icon (&hearts;) will appear in the activity log.</p>
		 <p>Not every &hearts; hangout results in pregnancy.</p>
     <h5>Duration of Pregnancy</h5>
     <p>Pregnancy lasts for approximately 30 days.</p>
     <ul>
      <li>First 10 days: "is pregnant"</li>
      <li>Second 10 days: "is very pregnant"</li>
      <li>Last 10 days: "is near birthing!"</li>
     </ul>
     <h5>The Effects of Pregnancy</h5>
     <p>As the pregnancy goes on, the pregnant pet will become less physically active; they will prefer performing indoor activites such as inventing and crafting, rather than going out to hunt and defeat monsters.</p>
     <p>Additionally, during the last half of pregnancy, the pregnant pet requires more food every hour, and becomes tired faster.</p>
     <p>When the pet does give birth, the pet will become very tired.</p>
     <h5>The Number and Kinds of Pets Born</h5>
     <p>Pets give birth to between 1 and 3 pets if you do not have the Breeder's License, between 1 and 5 if you do.  The number of pets averages on the low end of this range.</p>
     <p>The kinds of pets born depend on the parents.  Babies tend to have the same number of limbs, length of ears and tail, skin-type (hairy, scaly...), etc as their parents.</p>
     <p>For example, because mice, dogs, and cats all have four legs, ears, long tails, and fur, it is actually very likely for one of these to give birth to the other!  PsyPets are strange creatures.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
