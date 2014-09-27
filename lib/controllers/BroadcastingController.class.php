<?php
class BroadcastingController extends psyFrameworkController
{
  public function RunIndex(&$args = array())
  {
    header('Location: http://cardamom.psypets.net/broadcasting');
    exit();
  }
}
?>
