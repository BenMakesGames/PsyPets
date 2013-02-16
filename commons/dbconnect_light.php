<?php
require_once 'commons/settings_light.php';
require_once 'lib/handydb.class.php';
require_once 'commons/old_db_code_support.php';

ini_set('session.bug_compat_warn', 0);
ini_set('session.bug_compat_42', 0);

$database = new HandyDB();
