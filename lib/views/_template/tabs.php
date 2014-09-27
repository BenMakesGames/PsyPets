<ul class="tabbed">
<?php
foreach($_tabs as $title=>$link)
{
  if($link === false)
    echo '<li>' . $title . '</li>';
  else if($link == '')
    echo '<li class="activetab"><a href="">' . $title . '</a></li>';
  else
    echo '<li><a href="' . $link . '">' . $title . '</a></li>';
}
?>
</ul>
