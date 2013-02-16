<?php
require_once 'commons/tutorial/css.php';
?>
<script type="text/javascript">
function tut_close()
{
  $('#tutorial').hide();
}
</script>
<div id="tutorial">
 <img src="gfx/shim.png" height="200" width="1" align="left" alt="" />
 <span id="tutorial_text">
  <p>Oh, you found the pet equipment page!  Excellent!</p>
  <p>Equipment helps pets perform tasks better, for example a sword will help a pet defeat monsters, while a pickaxe will help with mining.</p>
  <p>The best equipment has requirements that must be met by your pet to be used... a huge hammer might require great strength to lift, or a magic wand may require intelligence to command.</p>
  <p>A pet can only equip up to one tool at a time, so definitely try to pick equipment that helps a pet do what it's best at (it'd be silly to equip a hunter with a sewing needle, for example).</p>
  <ul><li><a href="#" onclick="tut_close(); return false;">(end dialog)</a></li></ul>
 </span>
</div>
