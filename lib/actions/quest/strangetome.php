<?php
if($okay_to_be_here !== true)
  exit();
  
$the_right_key = $database->FetchSingle('
  SELECT *
  FROM monster_inventory
  WHERE
    itemname=\'Teeny-tiny Book Key\'
    AND user=' . quote_smart($user['user']) . '
  LIMIT 1
');

if($the_right_key)
{
?>
<p class="dim"><i>(It's locked with a lock which is exactly the shape of the Teeny-tiny Book Key you found earlier!  Ta-da!)</i></p>
<p class="dim"><i>(The first thing you notice is that the pages are false!  They open up to reveal a smaller compartment with a single sheet of paper that appears to have been reused.  It starts thus:)</i></p>
<p>beginning to lose hope.  Fred and I have still found no signs of any human life.</p>
<p class="dim"><i>(Then the writing starts up again, written with an obviously-different pen.)</i></p>
<p>AMELIA!  I w<u>r</u>ote this!  YOU wrote this!  I know you/I won't believe it at first, but this is our handwriting, and one of the pages from our old journal...</p>
<p>I know I'm going to be really freaked out when I find this again, but please bel<u>i</u>eve me that this is not a joke!  You have to get this message to the surface by any means necessary!  And secretly!  Not even <u>F</u>red can know...</p>
<p>I don't have much paper with which to convince you.  You just have to trust... yourself.</p>
<p style="text-align:center;">...</p>
<p>Hello, whoever you are.  I'm glad you were able to <u>f</u>ind my journal, and I'm sorry it was so well-hidden.  I've had to be careful.</p>
<p>As you can see, I've already used some of this paper explaining things to myself, so forgive my brevity.</p>
<p>The aliens - the Ancient<u>S</u>, you call them - are building something terrible.  It won't be done for... sometime late 2012, surface time.</p>
<p>I don't know exactly what will happen when they start it up.  Time passes differently down here, and it's because of something they built long ago.  I think the device they're building now does so<u>m</u>ething similar... something that alters how time and space interact?</p>
<p>I'm sorry I don't know the details, but I can <u>t</u>ell you this: the Ancients believe that planet Earth - inside and out - belongs to them; they have the m<u>e</u>ans and will to take it by force, and the device they're building is somehow the key to their success.</p>
<p>There's one more thing: there's a group on the surface in your time - scientists that call themselves "HERG".  The aliens are monitoring them very closely - more closely than makes sense to me.  Anything HERG knows, the Ancients know! The scientists must NOT learn about the things I have told you.</p>
<p style="text-align:center;">...</p>
<p>I'm sorry, m<u>y</u>sterious stranger, and myself as well.  You must both be confused.  But please believe that what I've said is tr<u>u</u>e, and good luck to both of you!</p>
<?php
}
else
{
?>
<p><i>(Son of a-- it's locked!  The lock appears to be very intricate, too... you worry that attempting to force it might damage the book.)</i></p>
<?php
}
?>
