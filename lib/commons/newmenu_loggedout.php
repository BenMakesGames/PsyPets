<div>
 <ul class="jd_menu jd_menu_slate">
  <li><a href="#" onclick="return false;">Services</a>
   <ul>
    <li><a href="/index.php">Latest News</a></li>
    <li><a href="/resetpass.php">Reset Password</a></li>
    <li><a href="/signup.php">Sign Up!</a></li>
    <li><a href="/activate.php">Activate Account</a></li>
   </ul>
  </li>
  <li><a href="#" onclick="return false;">Help</a>
   <ul>
    <li><a href="/help/">Help Desk</a></li>
    <li><a href="/encyclopedia.php">Item Encyclopedia</a></li>
    <li><a href="/petencyclopedia.php">Pet Encyclopedia</a></li>
<?php
if(strlen($wiki) > 0)
  echo '    <li class="separator"></li><li><a href="http://' . $SETTINGS['wiki_domain'] . '/' . $wiki . '">About This Page</a></li>';
?>
    <li class="separator"></li>
    <li><a href="/admincontact.php">Administrators</a></li>
    <li class="separator"></li>
    <li><a href="/statistics.php">Statistics</a></li>
   </ul>
  </li>
<?php
if($user['idnum'] > 0)
{
  require_once 'commons/moonphase.php';

  echo '<li style="float:right;">' . moon_graphic() . ' ' . local_time_short($now, $user['timezone'], $user['daylightsavings']) . '</li>';
}
?>
 </ul>
</div>
<script type="text/javascript">
$(function(){
  $('ul.jd_menu').jdMenu();
  // Add menu hiding on document click
  $(document).bind('click', function() {
    $('ul.jd_menu ul:visible').jdMenuHide();
  });
});
</script>
