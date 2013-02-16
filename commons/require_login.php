<?php include 'commons/html.php'; ?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Login Required</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php
include 'commons/header_2.php';
?>
     <h4>Login Required</h4>
		 <p class="failure">You must be logged in to access this page.</p>
		 <p>Log in using the fields above.</p>
		 <p>Don't have an account?  <em><strong>Ridiculous!</strong></em>  <a href="/signup.php">Create one now</a> <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/emote/hee.gif" class="inlineimage" alt=":)" width="16" height="16" /></p>

<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
