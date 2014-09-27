<?php
$prize = '100 moneys';

echo '"' . substr($prize, -7) . '"<br />';
echo '"' . substr($prize, 0, strlen($prize) - 7) . '"';
?>
