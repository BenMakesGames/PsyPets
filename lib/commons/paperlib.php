<?php
function unfold_airplane()
{
  if(mt_rand(1, 50) == 1)
    return 'Airship Mooring Blueprint';
  else if(mt_rand(1, 10) == 1)
    return 'Torn Paper';
  else
    return 'Paper';
}

function unfold_boat()
{
  if(mt_rand(1, 80) == 1)
    return 'Moat Blueprint';
  else if(mt_rand(1, 10) == 1)
    return 'Torn Paper';
  else
    return 'Paper';
}

function unfold_crane()
{
  if(mt_rand(1, 10) == 1)
    return 'Torn Paper';
  else
    return 'Paper';
}

function unfold_hat()
{
  if(mt_rand(1, 3000) == 1)
    return (mt_rand(1, 2) == 1 ? 'Scroll of Monster Summoning' : 'Food-Summoning Scroll');
  else if(mt_rand(1, 500) == 1)
    return 'Skeleton Key Blueprint';
  else if(mt_rand(1, 10) == 1)
    return 'Torn Paper';
  else
    return 'Paper';
}
?>
