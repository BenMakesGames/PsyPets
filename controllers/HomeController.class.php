<?php
class HomeController extends psyFrameworkController
{
  public function RunIndex(&$args = array())
  {
    $this->_view = '$home';
/*
    $content['title'] = 'Home';

		$content['npc'] = array(
			'name' => 'Kim Littrell',
		  'url' => 'petsheltergirl-2.png',
			'width' => 350,
			'height' => 450'
		);
*/
    return $content;
  }
}
?>
