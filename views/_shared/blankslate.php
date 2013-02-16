<?php
require FRAMEWORK_ROOT . 'views/_template/header.php';

require FRAMEWORK_ROOT . 'views/_template/page_title.php';

if(count($_tabs) > 0)
  require FRAMEWORK_ROOT . 'views/_template/tabs.php';

if(count($_npc) > 0)
  require FRAMEWORK_ROOT . 'views/_template/npc.php';

echo $_content;

require FRAMEWORK_ROOT . 'views/_template/footer.php';
?>
