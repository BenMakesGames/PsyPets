<div id="ingamead">
<?php
$enemies = fetch_multiple('
  SELECT enemyid
  FROM psypets_user_enemies
  WHERE userid=' . (int)$user['idnum'] . '
');

if(count($enemies) > 0)
{
  foreach($enemies as $enemy)
    $enemy_ids[] = $enemy['enemyid'];

  $ads = fetch_multiple('
    SELECT idnum,userid,ad,permanent
    FROM psypets_advertising
    WHERE
      userid NOT IN(' . implode(',', $enemy_ids) . ')
      AND (
        permanent=\'yes\'
        OR expirytime>' . $now . '
      )
  ');
}
else
{
  $ads = fetch_multiple('
    SELECT idnum,userid,ad,permanent
    FROM psypets_advertising
    WHERE
      permanent=\'yes\'
      OR expirytime>' . $now . '
  ');
}

shuffle($ads);

foreach($ads as $ad)
{
  if(!in_array($ad['userid'], $tv_ignore))
  {
    $this_ad = $ad;
    break;
  }
}

$this_user = get_user_byid($this_ad['userid']);

$formatted_ad = nl2br(format_text($this_ad['ad']));

echo
  '<p>' . $formatted_ad . '</p>',
  '<p class="centered">(<i>paid for by <a href="/residentprofile.php?resident=' . link_safe($this_user['display']) . '">' . $this_user['display'] . '</a></i>)</p>'
;

if($this_ad['permanent'] == 'no')
{
?>
<hr />
<h5>Rate This Ad<a href="/help/advertising.php" class="help">?</a></h5>
<div id="ratead">
<ul class="plainlist">
<li><a href="/ratead.php?ad=<?= $this_ad['idnum'] ?>&option=1" onclick="return ratead(<?= $this_ad['idnum'] ?>, 1);"><img src="/gfx/emote/yeah.gif" border="0" alt="" /> nice!</a></li>
<li><a href="/ratead.php?ad=<?= $this_ad['idnum'] ?>&option=2" onclick="return ratead(<?= $this_ad['idnum'] ?>, 2);"><img src="/gfx/emote/eh.gif" border="0" alt="" /> so-so</a></li>
<li><a href="/ratead.php?ad=<?= $this_ad['idnum'] ?>&option=3" onclick="return ratead(<?= $this_ad['idnum'] ?>, 3);"><img src="/gfx/emote/aw.gif" border="0" alt="" /> bad</a></li>
<li><a href="/ratead.php?ad=<?= $this_ad['idnum'] ?>&option=4" onclick="return ratead(<?= $this_ad['idnum'] ?>, 4);"><img src="/gfx/emote/grr.png" border="0" alt="" /> inappropriate!</a></li>
</ul>
</div>
<?php
}
?>
</div>
