<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;
$RECOUNT_INVENTORY = true;

delete_inventory_byid($this_inventory['idnum']);

if(mt_rand(1, 5) != 1)
{
  echo '<p>The cookie says...</p>';
  
  $a = mt_rand(1, 8);
  
  if($a == 1)
    echo '<p><b>Parse Error:</b> syntax error, unexpected T_STRING</p>';
  else if($a == 2)
    echo '<p>Exception in thread "main" java.lang.ArrayIndexOutOfBoundsException: 49</p>';
  else if($a == 3)
    echo '<pre style="color:white; background-color:blue; font-family:Courier, monospace;">*** STOP: 0x000000ED (0x86377738, 0xC000014F, 0x00000000, 0x00000000)</pre>';
  else if($a == 4)
    echo '<p>The requested URL was not found on this server.  Additionally, a 404 Not Found error was encountered while trying to use an ErrorDocument to handle the request.</p>';
  else if($a == 5)
    echo '<p>Sorry, a system error has occurred. (Error -11)</p>';
  else if($a == 6)
    echo '<pre style="color:white; background-color:black; font-family:Courier, monospace;">Not ready reading drive C<br />Abort, Retry, Fail?</pre>';
  else if($a == 7)
    echo '<pre style="color:#0f0; background-color:black; font-family:Courier, monospace;">Keyboard failure<br />&nbsp;Strike the F1 key to continue, F2 to run the setup utility</pre>';
  else if($a == 8)
    echo '
      <pre style="color:white; background-color:black; font-family:Courier, monospace;">General Protection Fault at eip=419; flags=3212<br />
      eax=00000300 ebx=003f0021 ecx=00000001 edx=178b00bf esi=00000000 edi=003efffe<br />
      ebp=00000001 esp=00003ffa cs=87 ds=bf es=b7 fs=0 gs=0 ss=a7 error=0000</pre>
    ';
}
else
{
  echo '<p>Oh!  There\'s something inside!</p>',
       '<p><i>(You received a Secret Password!)</i></p>';

  add_inventory($user['user'], '', 'Secret Password', 'Found in a ' . $this_inventory['itemname'], $this_inventory['location']);
}
?>
