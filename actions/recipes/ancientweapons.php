<?php
if($okay_to_be_here !== true)
  exit();
?>
<em>This tome is not only clearly ancient, but has barely survived a raging fire.</em></p>
<?php
if(rand() % 3 == 0)
{
?>
<p><em>You open it as carefully as possible, but your light grip loses hold of the cover, which thuds down on to the book, reducing it to ashes.</em></p>
<?php
  delete_inventory_byid($_GET["idnum"]);
  
  $AGAIN_WITH_ANOTHER = true;
}
else
{
?>
<p><em>Knowing that flipping through its contents would be too risky, you chance opening the book to whatever page it naturally falls on:</em></p>
<div style="padding:10px; margin:5px; border:1px solid #ccc;">
<?php
  $AGAIN_WITH_SAME = true;

  $a = mt_rand(1, 6);

  if($a == 1)
    echo "<center><img src=\"/special_graphic.php?id=1&item=" . $_GET["idnum"] . "\" /></center>";
  else if($a == 2)
    echo "<p class=\"nomargin\">[...] spoke of the drink to be had on this day.  It was Fortune's Tea, a tea made with 4-Leaf Clovers.  \"Let no one drink or eat anything but [...]</p>";
  else if($a == 3)
    echo "<p class=\"nomargin\">[...] this, the Fatal Crossing of the Evening and the Morning, forged of Pyrestone and drenched in Holy Water.  The world shall know [...]</p>";
  else if($a == 4)
    echo "<p class=\"nomargin\">Lesson 3:  If you see a stranger, follow him!</p>";
  else if($a == 5)
    echo "<p class=\"nomargin\">[...] which makes the <img src=\"//saffron.psypets.net/gfx/ancientscript/dae.png\" alt=\"dae\" class=\"inlineimage\"><img src=\"//saffron.psypets.net/gfx/ancientscript/si.png\" alt=\"si\" class=\"inlineimage\"><img src=\"//saffron.psypets.net/gfx/ancientscript/ka.png\" alt=\"ka\" class=\"inlineimage\"> [...] in part of Wild Oats [...]</p>";
  else if($a == 6)
    echo "<p class=\"nomargin\">[...] --ow why the world's alchemists never succeeded at obtaining immortali-- [...] --<strong>ple Pebble</strong> could create life, but-- [...] --s not enough.  One must master Life, Death, and that which falls between: <strong>Love</strong>.  [...] --cients knew thi-- [...] --Kaera, Ri-- [...]</p>";

  echo '</div>';
}
?>
