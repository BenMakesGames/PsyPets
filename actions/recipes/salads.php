<?php
if($okay_to_be_here !== true)
  exit();

$baba_ghanoush = get_item_byname('Baba Ghanoush');
$celery_victor = get_item_byname('Celery Victor');
$chilean_salad = get_item_byname('Chilean Salad');
?>
<h4>Tasty Salads</h4>
<table>
 <tr><td valign="top"><?= item_display($baba_ghanoush, '') ?></td><td>
  <h5>Baba Ghanoush</h5>
  <p>This Eastern Mediterranean salad is made of Eggplant, Tomato, and Onion.</p>
 </td></tr>
 <tr><td valign="top"><?= item_display($celery_victor, '') ?></td><td>
  <h5>Celery Victor</h5>
  <p>Invented in 1910 in America, this salad is made from Celery simmered in Chicken Broth, chilled in Vinegar, and served with Peppers over Leafy Cabbage.</p>
 </td></tr>
 <tr><td valign="top"><?= item_display($chilean_salad, '') ?></td><td>
  <h5>Chilean Salad</h5>
  <p>Tomato, Cilantro and Onion, topped with Olive Oil.  Add Fire Spice for a Spicy Chilean Salad.</p>
 </td></tr>
</table>