<?php
function get_global($name)
{
  $command = 'SELECT * FROM monster_globals WHERE `name`=' . quote_smart($name) . ' LIMIT 1';
  $global = fetch_single($command, 'fetching global value');

  if($global['type'] == 'string')
    return $global['value'];
  else if($global['type'] == 'number')
    return (float)$global['value'];
  else if($global['type'] == 'list')
    return explode(',', $global['value']);
  else if($global['type'] == 'yorn')
    return ($global['value'] == 'yes' ? 'yes' : 'no');
}

function set_global($name, $value)
{
  if(get_magic_quotes_gpc())
    $value = stripslashes($value);

  fetch_none('UPDATE monster_globals SET `value`=' . quote_smart($value) . ' WHERE `name`=' . quote_smart($name) . ' LIMIT 1');
}
