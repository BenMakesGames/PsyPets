<?php
define('WEB_ROOT', realpath(dirname(dirname(dirname(__FILE__))) . '/web'));
define('LIB_ROOT', realpath(dirname(dirname(__FILE__))));

set_include_path(get_include_path() . PATH_SEPARATOR . LIB_ROOT);

function __autoload($class_name)
{
  if(file_exists(LIB_ROOT . '/models/' . $class_name . '.class.php'))
    require_once LIB_ROOT . '/models/' . $class_name . '.class.php';
}
