<?php
$__ZEBRA_STRIPES = 0;

function zebra_stripe_reset()
{
  global $__ZEBRA_STRIPES;

  $__ZEBRA_STRIPES = 0;
}

function zebra_stripe($rows = array('row', 'altrow'))
{
  global $__ZEBRA_STRIPES;

  return $rows[($__ZEBRA_STRIPES++) % count($rows)];
}
?>
