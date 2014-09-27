<?php
define('WEB_ROOT', $_SERVER['DOCUMENT_ROOT']);
define('LIB_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/../lib');

function __autoload($class_name)
{
  if(file_exists(LIB_ROOT . '/models/' . $class_name . '.class.php'))
    require_once LIB_ROOT . '/models/' . $class_name . '.class.php';
}
