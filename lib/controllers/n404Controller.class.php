<?php
class n404Controller extends psyFrameworkController
{
  public function RunIndex(&$args = array())
  {
    $this->_view = '$blankslate';

    $content['user'] = User::GetBySession();
  
    $content['title'] = array(array(false, 'Four-oh-four!'));

    $content['npc'] = array(
      'graphic' => 'npcs/receptionist.png',
      'width' => 350,
      'height' => 275,
      'name' => 'Claire Silloway',
    );

    $content['npc']['dialog'] = '
      <p>The requested page was not found!</p>
      <ul>
       <li><p>If a link within ' . $SETTINGS['site_name'] . ' lead you to this page, that\'s almost certainly an error!  <a href="/writemail.php?sendto=' . $SETTINGS['author_resident_name'] . '">Let ' . $SETTINGS['author_resident_name'] . ' know, so he can fix it up</a> :)</p></li>
       <li><p>If a link from somewhere else entirely lead you here, it might be nice to let that site\'s administrator or editor know that their links are incorrect or out of date.</p></li>
      </ul>
		';

    $request = $this->_psyframework->Request();

    $content['content'] = '<p><i>(You requested ' . $request['controller'] . '/' . $request['method'] . ')</i></p>';

    return $content;
  }
}
?>
