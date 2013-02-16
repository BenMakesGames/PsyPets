<?php
require_once 'commons/petstatlib.php';

function passion_for(&$this_pet, &$for_that_pet)
{
	$passion = $this_pet['attraction_to_' . $for_that_pet['gender'] . 's'];
	
	// reduce incest >_>
	if(are_immediate_family_members($this_pet, $for_that_pet))
	{
		$passion = round($passion / 3);
	}
	
	return $passion;
}

function describe_relationship($pet, $relationship)
{
  $descriptions = array();

  if($relationship['passion'] >= 45)
    $descriptions[] = 'hot';
  else if($relationship['passion'] >= 25)
    $descriptions[] = 'attractive';
  else if($relationship['passion'] >= 10)
    $descriptions[] = 'cute';

  if($relationship['intimacy'] >= 45)
    $descriptions[] = 'awesome to be with';
  else if($relationship['intimacy'] >= 25)
    $descriptions[] = 'fun to be with';
  else if($relationship['intimacy'] >= 10)
    $descriptions[] = 'nice to see';

  if($relationship['commitment'] >= 45)
    $descriptions[] = 'irreplaceable';
  else if($relationship['commitment'] >= 25)
    $descriptions[] = 'greatly valued';
  else if($relationship['commitment'] >= 10)
    $descriptions[] = 'valued';

  if(count($descriptions) == 0)
    return 'an acquaintance';
  else
  {
    $description = list_nice($descriptions, ', and ');

    if($description{0} == 'a')
      return $description;
    else
      return $description;
  }
}

function preference_description($attraction)
{
  if($attraction > 80)
    return 'is crazy about';
  else if($attraction > 60)
    return 'really likes';
  else if($attraction > 40)
    return 'likes';
  else if($attraction > 20)
    return 'has little interest in';
  else
    return 'isn\'t interested in';
}

function do_adventuring_hang_out(&$this_pet, &$other_pet)
{
  $this_relationship = get_pet_relationship($this_pet, $other_pet);
  $other_relationship = get_pet_relationship($other_pet, $this_pet);

  advance_adventuring_relationship($this_pet, $this_relationship, $other_pet, $other_relationship);
}

function do_friendly_hang_out($hour, &$this_pet, &$other_pet)
{
  $this_relationship = get_pet_relationship($this_pet, $other_pet);
  $other_relationship = get_pet_relationship($other_pet, $this_pet);

  if(mt_rand(1, 100) <= sex_suggest($this_pet, $other_pet, $this_relationship, $other_relationship))
  {
    $hung_out = suggest_sexy_hang_out($hour, $this_pet, $other_pet, $this_relationship, $other_relationship);
    
    if($hung_out)
      return;
  }

	$other_pet_link = '<a href="/petprofile.php?petid=' . $other_pet['idnum'] . '">' . $other_pet['petname'] . '</a>';
	$this_pet_link = '<a href="/petprofile.php?petid=' . $this_pet['idnum'] . '">' . $this_pet['petname'] . '</a>';

	$effects = array(
		'esteem' => gain_esteem($this_pet, mt_rand(1, 3)),
		'love' => gain_love($this_pet, mt_rand(3, 6)),
		'safety' => gain_safety($this_pet, mt_rand(2, 5)),
	);

  $this_pet_owner = get_user_byuser($this_pet['user'], 'idnum');
  $other_pet_owner = get_user_byuser($other_pet['user'], 'idnum');

	add_logged_event($this_pet_owner['idnum'], $this_pet['idnum'], $hour, ($hour == 0 ? 'realtime' : 'hourly'), 'hangout', '<span class="success">' . $this_pet['petname'] . ' went over to ' . $other_pet_link . '\'s to hang out.</span>', $effects);
  add_logged_event($other_pet_owner['idnum'], $other_pet['idnum'], 0, 'realtime', 'hangout', '<span class="success">' . $this_pet_link . ' came over to hang out with ' . $other_pet['petname'] . '.</span>', $effects);

  advance_friendly_relationship($this_pet, $this_relationship, $other_pet, $other_relationship);
}

function suggest_sexy_hang_out($hour, &$this_pet, &$other_pet, &$this_relationship, &$other_relationship)
{
  $total_sex_agree =
    sex_agree($other_pet, $this_pet, $other_relationship, $this_relationship) + // how much YOU want to
    sex_agree($this_pet, $other_pet, $this_relationship, $other_relationship) * ((10 - $other_pet['independent']) / 10) // and how much the partner wants to, multiplied by your DEpendence
  ;

  // the more DEpendent the other pet, the more likely it is to agree (based on how excited the suggesting pet is about it)
  if(mt_rand(1, 100) <= $total_sex_agree)
  {
    do_sexy_hang_out($hour, $this_pet, $other_pet, $this_relationship, $other_relationship);
    return true;
  }
  else
  {
    $hurt_chance = sex_suggest($this_pet, $other_pet, $this_relationship, $other_relationship) * (100 - min(100, sex_agree($other_pet, $this_pet, $other_relationship, $this_relationship)));
    
    if(mt_rand(1, 100) <= $hurt_chance)
    {
      do_fighting_hang_out($hour, $this_pet, $other_pet, $this_relationship, $other_relationship);
      return true;
    }
    else
      return false;
  }
}

function do_fighting_hang_out($hour, &$this_pet, &$other_pet, &$this_relationship, &$other_relationship)
{
	$other_pet_link = '<a href="/petprofile.php?petid=' . $other_pet['idnum'] . '">' . $other_pet['petname'] . '</a>';
	$this_pet_link = '<a href="/petprofile.php?petid=' . $this_pet['idnum'] . '">' . $this_pet['petname'] . '</a>';

	$effects = array(
		'esteem' => -lose_stat($this_pet, 'esteem', mt_rand(1, 3)),
		'love' => -lose_stat($this_pet, 'love', mt_rand(1, 3)),
		'safety' => -lose_stat($this_pet, 'safety', mt_rand(1, 3)),
	);

  $this_pet_owner = get_user_byuser($this_pet['user'], 'idnum');
  $other_pet_owner = get_user_byuser($other_pet['user'], 'idnum');

	add_logged_event($this_pet_owner['idnum'], $this_pet['idnum'], $hour, ($hour == 0 ? 'realtime' : 'hourly'), 'hangout', '<span class="failure">' . $this_pet['petname'] . ' went over to ' . $other_pet_link . '\'s to hang out, but ended up arguing!</span>', $effects);
  add_logged_event($other_pet_owner['idnum'], $other_pet['idnum'], 0, 'realtime', 'hangout', '<span class="failure">' . $this_pet_link . ' came over to hang out with ' . $other_pet['petname'] . ', but ended up arguing!</span>', $effects);

  // pet 1
  $this_relationship['commitment'] = ceil($this_relationship['commitment'] * min(200, $this_relationship['intimacy'] + $other_relationship['commitment']) / 200);
  $this_relationship['intimacy'] = ceil($this_relationship['intimacy'] * min(200, $this_relationship['commitment'] + $other_relationship['intimacy']) / 200);
  $this_relationship['passion'] = ceil($this_relationship['passion'] * min(200, $this_relationship['commitment'] + $other_relationship['passion']) / 200);

  update_pet_relationship($this_relationship, array('intimacy', 'passion', 'commitment'));

  // pet 2
  $other_relationship['commitment'] = ceil($other_relationship['commitment'] * min(200, $other_relationship['intimacy'] + $this_relationship['commitment']) / 200);
  $other_relationship['intimacy'] = ceil($other_relationship['intimacy'] * min(200, $other_relationship['commitment'] + $this_relationship['intimacy']) / 200);
  $other_relationship['passion'] = ceil($other_relationship['passion'] * min(200, $other_relationship['commitment'] + $this_relationship['passion']) / 200);
  
  update_pet_relationship($other_relationship, array('intimacy', 'passion', 'commitment'));
}

function do_sexy_hang_out($hour, &$this_pet, &$other_pet, &$this_relationship, &$other_relationship)
{
  if($this_pet['prolific'] == 'yes' && $other_pet['prolific'] == 'yes')
  {
    if($this_pet['gender'] == 'male' && $other_pet['gender'] == 'female' && $other_pet['pregnant_asof'] == 0)
    {
      // gizubi adjustment, from -10% to +10%
      // equipment gives +3% per point of pregnancy
      $pregnancy_chance = 40 + floor($god['gijubi']['attitude'] / 10) + $other_pet['pregnancy_increase'] * 3;

      // pets less than 6 weeks old have a reduced chance of pregnancy
      if(time() - $pet['birthday'] < (42 * 24 * 60 * 60))
        $pregnancy_chance = floor($pregnancy_change * (time() - $pet['birthday']) / (42 * 24 * 60 * 60));

      if(mt_rand(1, 100) <= $pregnancy_chance)
      {
        record_pet_stat($other_pet['idnum'], 'Became Pregnant', 1);
        record_pet_stat($this_pet['idnum'], 'Impregnated Another Pet', 1);

        $other_pet['pregnant_asof'] = 1;

        $other_pet['pregnant_by'] = $this_pet['idnum'] . ',' . $this_pet['bloodtype'] . ',' . $this_pet['graphic'];
      }
    }
    else if($other_pet['gender'] == 'male' && $this_pet['gender'] == 'female' && $this_pet['pregnant_asof'] == 0)
    {
      // gizubi adjustment, from -10% to +10%
      // equipment gives +3% per point of pregnancy
      
      // after 2012.08.20, reduce the base pregnancy rate to 30 from 80
      $base_rate = (time() >= 1345438800 ? 30 : 80);
      
      $pregnancy_chance = $base_rate + floor($god['gijubi']['attitude'] / 10) + $this_pet['pregnancy_increase'] * 3;

      // pets less than 6 weeks old have a reduced chance of pregnancy
      if(time() - $pet['birthday'] < (42 * 24 * 60 * 60))
        $pregnancy_chance = (int)($pregnancy_change * (time() - $pet['birthday']) / (42 * 24 * 60 * 60));

      if(mt_rand(1, 100) <= $pregnancy_chance)
      {
        record_pet_stat($this_pet['idnum'], 'Became Pregnant', 1);
        record_pet_stat($other_pet['idnum'], 'Impregnated Another Pet', 1);

        $this_pet['pregnant_asof'] = 1;

        $this_pet['pregnant_by'] = $other_pet['idnum'] . ',' . $other_pet['bloodtype'] . ',' . $other_pet['graphic'];
      }
    }
  } // if both pets are fertile

	$other_pet_link = '<a href="/petprofile.php?petid=' . $other_pet['idnum'] . '">' . $other_pet['petname'] . '</a>';
	$this_pet_link = '<a href="/petprofile.php?petid=' . $this_pet['idnum'] . '">' . $this_pet['petname'] . '</a>';

	$effects = array(
		'esteem' => gain_esteem($this_pet, mt_rand(2, 4)),
		'love' => gain_love($this_pet, mt_rand(4, 7)),
		'safety' => gain_safety($this_pet, mt_rand(3, 6)),
	);

  $this_pet_owner = get_user_byuser($this_pet['user'], 'idnum');
  $other_pet_owner = get_user_byuser($other_pet['user'], 'idnum');

	add_logged_event($this_pet_owner['idnum'], $this_pet['idnum'], $hour, ($hour == 0 ? 'realtime' : 'hourly'), 'hangout', '<span class="success">' . $this_pet['petname'] . ' went over to ' . $other_pet_link . '\'s to hang out &hearts;</span>', $effects);
  add_logged_event($other_pet_owner['idnum'], $other_pet['idnum'], 0, 'realtime', 'hangout', '<span class="success">' . $this_pet_link . ' came over to hang out with ' . $other_pet['petname'] . ' &hearts;</span>', $effects);
  
	if($this_relationship === false)
    $this_relationship = create_new_relationship($this_pet, $other_pet);
    
  if($other_relationship === false)
    $other_relationship = create_new_relationship($other_pet, $this_pet);

  advance_sexy_relationship($this_pet, $this_relationship, $other_pet, $other_relationship);
}

function get_pet_relationship(&$this_pet, &$other_pet)
{
  $relationship = fetch_single('
    SELECT *
    FROM psypets_pet_relationships
    WHERE
      petid=' . (int)$this_pet['idnum'] . '
      AND friendid=' . (int)$other_pet['idnum'] . '
    LIMIT 1
  ');
  
  if($relationship === false)
    $relationship = create_new_relationship($this_pet, $other_pet);

  return $relationship;
}

function create_new_relationship($pet1, $pet2)
{
  global $now;

  $rel = array(
    'petid' => (int)$pet1['idnum'],
    'friendid' => (int)$pet2['idnum'],
    'firstmet' => $now,
    'intimacy' => mt_rand(1, floor(($pet2['extraverted'] + 2) / 2)),
    'passion' => round(mt_rand(0, 10) * $pet1['attraction_to_' . $pet2['gender'] . 's'] / 100),
    'commitment' => mt_rand(0, floor(($pet1['extraverted'] + 2) / 2)),
  );

  $GLOBALS['database']->FetchNone('
    INSERT INTO psypets_pet_relationships
    (petid, friendid, firstmet, intimacy, passion, commitment)
    VALUES
    (' . implode(', ', $rel) . ')
  ');

  return $rel;
}

function advance_adventuring_relationship($pet1, $rel1, $pet2, $rel2)
{
  advance_friendly_relationship($pet1, $rel1, $pet2, $rel2);
}

function advance_friendly_relationship($pet1, $rel1, $pet2, $rel2)
{
  // pet 1
  $rel1['intimacy']++;

	if(mt_rand(1, 100) <= $rel1['intimacy'])
		$rel1['commitment']++;

  if(mt_rand(1, 100) <= passion_for($pet1, $pet2))
		$rel1['passion'] += mt_rand(1, 2);

	if(mt_rand(1, 100) <= $rel1['passion'] || mt_rand(1, 100) <= $rel1['commitment'])
		$rel1['intimacy']++;

  update_pet_relationship($rel1, array('intimacy', 'passion', 'commitment'));

  // pet 2
  $rel2['intimacy']++;

	if(mt_rand(1, 100) <= $rel2['intimacy'])
		$rel2['commitment']++;

  if(mt_rand(1, 100) <= passion_for($pet2, $pet1))
		$rel2['passion'] += mt_rand(1, 2);

	if(mt_rand(1, 100) <= $rel2['passion'] || mt_rand(1, 100) <= $rel2['commitment'])
		$rel2['intimacy']++;

  update_pet_relationship($rel2, array('intimacy', 'passion', 'commitment'));
}

function advance_sexy_relationship($pet1, $rel1, $pet2, $rel2)
{
  // pet 1
  $rel1['passion']++;

	if(mt_rand(1, 100) <= $rel1['intimacy'])
		$rel1['commitment']++;

  if(mt_rand(1, 100) <= passion_for($pet1, $pet2))
    $rel1['passion'] += mt_rand(1, 2);

	if(mt_rand(1, 100) <= $rel1['passion'] || mt_rand(mt_rand(1, 50), 100) <= $rel1['commitment'])
		$rel1['intimacy']++;

  update_pet_relationship($rel1, array('intimacy', 'passion', 'commitment'));

  // pet 2
	$rel2['passion']++;

	if(mt_rand(1, 100) <= $rel2['intimacy'])
		$rel2['commitment']++;

  if(mt_rand(1, 100) <= passion_for($pet2, $pet1))
		$rel2['passion'] += mt_rand(1, 2);

	if(mt_rand(1, 100) <= $rel2['passion'] || mt_rand(1, 100) <= $rel2['commitment'])
		$rel2['intimacy']++;

  update_pet_relationship($rel2, array('intimacy', 'passion', 'commitment'));
}

function update_pet_relationship($rel, $stats)
{
  foreach($stats as $stat)
  {
    if($stat == 'intimacy' || $stat == 'passion' || $stat == 'commitment')
    {
      if($rel[$stat] > 100)
        $rel[$stat] = 100;
    }
  
    $sets[] = $stat . '=' . (int)$rel[$stat];
  }

  $GLOBALS['database']->FetchNone('
    UPDATE psypets_pet_relationships
    SET ' . implode(', ', $sets) . '
    WHERE
      petid=' . (int)$rel['petid'] . '
      AND friendid=' . (int)$rel['friendid'] . '
    LIMIT 1
  ');
}
