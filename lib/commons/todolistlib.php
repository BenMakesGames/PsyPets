<?php
$TODO_LIST_CATEGORIES = array(
  'art',
  'design',
  'design/The Pets Are Alive',
  'design/The Pets Are the Primary Agents',
  'design/You Are Part of a Small Community',
  'engineering',
  'engineering/Bug Fix',
  'engineering/Performance',
);

function render_idea_details(&$idea)
{
  list($category, $sub_category) = explode('/', $idea['category']);
    
  if($category == 'design')
  {
    if($sub_category == '')
      echo '<h5>Game-design</h5>';
    else
    {
      if($sub_category == 'The Pets Are Alive')
        echo '<a href="/help/design-philosophies.php"><img src="//' . $SETTINGS['static_domain'] . '/gfx/help/pillar_living_pets.png" width="75" height="150" alt="" align="right" /></a>';
      else if($sub_category == 'The Pets Are the Primary Agents')
        echo '<a href="/help/design-philosophies.php"><img src="//' . $SETTINGS['static_domain'] . '/gfx/help/pillar_pets_first.png" width="75" height="150" alt="" align="right" /></a>';
      else if($sub_category == 'You Are Part of a Small Community')
        echo '<a href="/help/design-philosophies.php"><img src="//' . $SETTINGS['static_domain'] . '/gfx/help/pillar_community.png" width="75" height="150" alt="" align="right" /></a>';
        
      echo '<h5>Game-design: ' . $sub_category . '<a href="/help/design-philosophies.php" class="help">?</a></h5>';
    }
  }
  else if($category == 'art')
    echo '<h5>Art</h5>';
  else if($category == 'engineering')
  {
    if($sub_category == 'Bug Fix')
        echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/help/engineering_bugs.png" width="75" height="150" alt="" align="right" />';
  
    if($sub_category == '')
      echo '<h5>Engineering</h5>';
    else
      echo '<h5>Engineering: ' . $sub_category . '</h5>';
  }
  else
    echo '<h5>Uncategorized</h5>';
    
  if($idea['ldesc'] != '')
    echo '<p>' . format_text($idea['ldesc']) . '</p>';
}
?>
