<?php
require_once 'commons/settings.php';

if($SETTINGS['game_donatetag'] !== false)
{
?>
<a href="buyfavors.php"><img src="gfx/donate3.png" id="buyfavors" width="105" height="95" alt="Buy Favor with PayPal to get custom items, custom avatars, pet resurrections, and more." /></a>
<?php
}
?>
