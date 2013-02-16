<?php
function xhtml_progress_bar($cur, $max, $width, $color)
{
  $progress = floor($cur * $width / $max);
  $percent = floor($cur * 100 / $max);

  if($progress > $width)
    $progress = $width;
  
  if($percent > 100)
    $percent_text = '100%+';
  else
    $percent_text = $percent . '%';
  
  return
    '<div style="width:' . $width . 'px;background-color:#ddd;border-radius:5px;height:10px;" title="' . $percent_text . '">' .
    '<div style="width:' . $progress . 'px;background-color:' . $color . ';border-radius:5px;height:10px;"></div>' .
    '</div>'
  ;
}
?>