<?php
require_once 'commons/psypetsformatting.php';
?>
<html>
<head>
<title><?= $SETTINGS['site_name'] ?> Markup Test</title>
<link rel="stylesheet" href="//<?= $SETTINGS['static_domain'] ?>/css/pp_markup.css" />
</head>
<body>
<?php
if($_POST['action'] == 'Preview')
{
  $original_text = $_POST['post'];
  
  echo '<div>' . $xhtml . '</div>';
  echo '<div>' . $original_text . '</div>';
}
?>
<form action="psypetsformattingtest.php" method="post">
<p><textarea name="post" cols="60" rows="20"><?= $original_text ?></textarea></p>
<p><input type="submit" name="action" value="Preview" /></p>
</form>
</body>
</html>