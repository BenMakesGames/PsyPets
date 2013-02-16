<script type="text/javascript">
function reveal_project_details(idnum)
{
  document.getElementById('project' + idnum).style.display = 'block';
  document.getElementById('reveal' + idnum).style.display = 'none'; 
}
</script>
<?php
function render_project_notes($formatted_text, $idnum)
{
  $notes = explode("\n", $formatted_text);
  $num_notes = count($notes);

  if($num_notes > 3)
  {
    $i = 0;
    foreach($notes as $note)
    {
      if($i == 0)
        echo $note . '<br />';
      else if($i == $num_notes - 1)
        echo $note;
      else if($i == 1)
        echo '<span id="project' . $idnum . '" style="display:none;">' . $note . '<br />';
      else if($i == $num_notes - 2)
        echo $note . '<br /></span><span id="reveal' . $idnum . '"><i><a href="#" onclick="reveal_project_details(' . $idnum . '); return false;">show complete history...</a></i><br /></span>';
      else
        echo $note . '<br />';
    
      ++$i;
    }
  }
  else
    return implode('<br />', $notes);
}
?>
