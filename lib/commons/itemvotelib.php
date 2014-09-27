<?php
function vote_xhtml($category, $vote)
{
  if($vote == 'no')
    echo '<b>no</b>';
  else
    echo '<a href="#" onclick="do_vote(\'' . $category . '\', \'no\'); return false;">no</a>';

  echo ' | ';

  if($vote == 'somewhat')
    echo '<b>somewhat</b>';
  else
    echo '<a href="#" onclick="do_vote(\'' . $category . '\', \'somewhat\'); return false;">somewhat</a>';

  echo ' | ';

  if($vote == 'yes')
    echo '<b>yes</b> ';
  else
    echo '<a href="#" onclick="do_vote(\'' . $category . '\', \'yes\'); return false;">yes</a>';
}
?>
