<?php
define('WEB_ROOT', $_SERVER['DOCUMENT_ROOT']);
set_include_path(get_include_path() . PATH_SEPARATOR . WEB_ROOT);

function __autoload($class_name)
{
  if(file_exists(WEB_ROOT . '/models/' . $class_name . '.class.php'))
    require_once WEB_ROOT . '/models/' . $class_name . '.class.php';
}
?>
