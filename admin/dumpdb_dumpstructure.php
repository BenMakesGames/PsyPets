<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/threadfunc.php';

if($admin['coder'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$table_match = trim($_GET['tables']);

$tables = $database->FetchMultiple(('SHOW TABLES');

$db_structure = array();

foreach($tables as $table)
{
  $table_name = reset($table);

  if($table_match == '*' || false /* some other matching stuff */)
  {
    $fields = $database->FetchMultiple(('DESCRIBE `' . $table_name . '`');
    
    foreach($fields as $field)
    {
      $db_structure[$table_name][$field['Field']] = array(
        'type' => $field['Type'],
        'null' => ($field['Null'] == 'YES'),
        'key' => $field['Key'],
        'default' => $field['Default'],
        'auto_increment' => ($field['Extra'] == 'auto_increment'),
      );
    }
  }
}

function json_encode_plus($var)
{
  $str = json_encode($var);
  $len = strlen($str);
  
  $new = '';
  $padding = 0;
  $quoting = false;
  
  for($x = 0; $x < $len; ++$x)
  {
    $char = $str{$x};
    
    // an unescaped quotation mark
    if($char == '"' && $str{$x - 1} != '\\')
      $quoting = !$quoting;
      
    if($quoting)
      $new .= $char;
    else
    {
      // a closing brace
      if($char == '}')
      {
        $padding -= 2;
        $new .= "\n" . str_repeat(' ', $padding);
      }

      $new .= $char;
    
      // an opening brace
      if($char == '{')
      {
        $padding += 2;
        $new .= "\n" . str_repeat(' ', $padding);
      }
      // a comma following a closing brace
      else if($char == ',' && $str{$x - 1} == '}')
      {
        $new .= "\n" . str_repeat(' ', $padding);
      }
    }
  }
  
  return $new;
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Database Dump Tools &gt; Structure Dump</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
 <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; <a href="/admin/dumpdb.php">Database Dump Tools</a> &gt; Structure Dump</h4>
<div style="overflow:auto; width:800px; height: 400px;"><pre><?= json_encode_plus($db_structure) ?></pre></div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
