<?php
$SKILL_ROLE_TYPES = array('Control', 'Damage', 'Defense', 'Support', 'Utility');

function fallback($skill, $key)
{
  return (array_key_exists($key, $skill) ? $skill[$key] : $skill['_' . $key]);
}

function render_rp_skill($skill, $extra_class = '')
{
?>
  <div class="rpskill <?= $skill['type'] ?> <?= $extra_class ?>">
  <h5><?= fallback($skill, 'name') ?> - Level <?= fallback($skill, 'level') ?> <?= fallback($skill, 'type') ?></h5>
<?php
  $cost_to_use = fallback($skill, 'cost_to_use');
  $duration = fallback($skill, 'duration');

  if($cost_to_use != '')
    echo '<p><b>Cost to use:</b> ', $cost_to_use, '</p>';
  if($duration != '')
    echo '<p><b>Duration:</b> ', $duration, '</p>';
?>
  <p><?= fallback($skill, 'description') ?></p>
  </div>
<?php
}

function render_rp_skills($skills)
{
  foreach($skills as $skill)
    render_rp_skill($skill);
}

function get_rp_skills($role = false)
{
  global $SKILL_ROLE_TYPES;

  if(!in_array($role, $SKILL_ROLE_TYPES))
    $role = false;

  return fetch_multiple('
    SELECT *
    FROM psypetsrp_skills
    ' . ($role ? 'WHERE FIND_IN_SET(\'' . $role . '\', role) > 0' : '') . '
    ORDER BY level ASC
  ');
}

function get_rp_skills_for_level($level)
{
  return fetch_multiple('
    SELECT *
    FROM psypetsrp_skills
    WHERE level<=' . (int)$level . '
    ORDER BY level ASC
  ');
}

function get_rp_skill($id)
{
  return fetch_single('
    SELECT *
    FROM psypetsrp_skills
    WHERE idnum=' . (int)$id . '
    LIMIT 1
  ');
}

function get_rp_character_skills($characterid)
{
  return fetch_multiple('
    SELECT
      a.idnum,a.type,a.skillid,
      b.name AS _name,b.level AS _level,b.type AS _type,
      b.cost_to_use AS _cost_to_use,b.description AS _description
    FROM
      psypetsrp_character_skills AS a
      LEFT JOIN psypetsrp_skills AS b
        ON a.skillid=b.idnum
    WHERE a.characterid=' . (int)$characterid . '
    ORDER BY b.level ASC
  ');
}

function get_rp_characters($userid)
{
  return fetch_multiple('
    SELECT a.*,b.name AS _story_name,b.idnum AS _story_id
    FROM psypetsrp_characters AS a
      LEFT JOIN psypetsrp_stories AS b ON a.storyid=b.idnum
    WHERE a.userid=' . (int)$userid . '
    ORDER BY b.name ASC
  ');
}

function get_rp_characters_in_stories($stories, $excluded_user_id)
{
  return fetch_multiple('
    SELECT a.*,b.name AS _story_name,b.idnum AS _story_id,c.display AS _owner_name
    FROM psypetsrp_characters AS a
      LEFT JOIN psypetsrp_stories AS b ON a.storyid=b.idnum
        LEFT JOIN monster_users AS c ON a.userid=c.idnum
    WHERE
      a.storyid IN (' . implode(',', $stories) . ') AND
      a.userid!=' . (int)$excluded_user_id . '
    ORDER BY b.name ASC
  ');
}

function get_my_story_ids($userid)
{
  $stories = get_my_stories($userid);
  
  $ids = array();
  
  foreach($stories as $story)
    $ids[] = $story['idnum'];

  return $ids;
}

function get_schedules()
{
  $data = fetch_multiple('SELECT * FROM psypetsrp_schedule');
  
  $schedule = array();
  
  foreach($data as $row)
    $schedule[$row['userid']][$row['hour']] = true;
  
  return $schedule;
}

function get_my_stories($userid)
{
  return fetch_multiple('
    SELECT *
    FROM psypetsrp_stories
    WHERE storytellerid=' . $userid . '
    ORDER BY name ASC
  ');
}

function get_rp_characters_as_storyteller($userid)
{
  return fetch_multiple('
    SELECT a.*,b.name AS _story_name,b.idnum AS _story_id,c.display AS _owner_name
    FROM psypetsrp_characters AS a
      LEFT JOIN psypetsrp_stories AS b ON a.storyid=b.idnum
        LEFT JOIN monster_users AS c ON a.userid=c.idnum
    WHERE b.storytellerid=' . (int)$userid . '
    ORDER BY b.name ASC
  ');
}

function get_rp_character($charid)
{
  return fetch_single('
    SELECT a.*,b.name AS _story_name,b.idnum AS _story_id
    FROM psypetsrp_characters AS a
      LEFT JOIN psypetsrp_stories AS b ON a.storyid=b.idnum
    WHERE
      a.idnum=' . (int)$charid . '
    LIMIT 1
  ');
}

function rp_train_character_hp(&$character)
{
  $character['training_points']--;
  $character['max_hp'] += 5;
  $character['cur_hp'] += 5;

  fetch_none('
    UPDATE psypetsrp_characters
    SET
      training_points=training_points-1,
      max_hp=max_hp+5,
      cur_hp=cur_hp+5
    WHERE
      idnum=' . (int)$character['idnum'] . '
    LIMIT 1
  ');
}

function rp_train_character_sp(&$character)
{
  $character['training_points']--;
  $character['max_sp'] += 5;
  $character['cur_sp'] += 5;

  fetch_none('
    UPDATE psypetsrp_characters
    SET
      training_points=training_points-1,
      max_hp=max_sp+5,
      cur_hp=cur_sp+5
    WHERE
      idnum=' . (int)$character['idnum'] . '
    LIMIT 1
  ');
}

function rp_train_character_skill(&$character, &$skill, $skill_type = '')
{
  $character['training_points']--;

  fetch_none('
    UPDATE psypetsrp_characters
    SET
      training_points=training_points-1
    WHERE
      idnum=' . (int)$character['idnum'] . '
    LIMIT 1
  ');
  
  fetch_none('
    INSERT INTO psypetsrp_character_skills
    (characterid, skillid, type)
    VALUES
    (' . (int)$character['idnum'] . ', ' . (int)$skill['idnum'] . ', ' . quote_smart($skill_type) . ')
  ');
}
?>