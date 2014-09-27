<?php
$QUEST_DESCRIPTIONS = array(
  'NPC introductions' => array(
    'title' => 'Introductions',
    'description' => 'Get to know your way around ' . $SETTINGS['site_name'] . '!',
    'done_step' => 4,
    'steps' => array(
      1 => 'Visit Kim at The Pet Shelter.',
      2 => 'Visit Nina at The Smithery.',
    )
  ),
  'Tip Quest!' => array(
    'title' => 'Newbie Among The Fennel',
    'description' => 'The Florist wants to thank you for reading all the tips she authored!',
    'done_step' => 2,
    'steps' => array(
      1 => 'Talk to The Florist to receive your prize.',
    )
  ),
  'Plushy Collection' => array(
    'title' => 'Nina, Plushy Collector',
    'description' => 'Nina is a huge fan of MyPlushy, and has asked if you\'d help her complete her collection.',
    'done_step' => 11,
    'steps' => array(
      1 => 'Find a ' . $PLUSHY_QUEST_LIST[0] . ' for Nina.',
      2 => 'Find a ' . $PLUSHY_QUEST_LIST[1] . ' for Nina.',
      3 => 'Find a ' . $PLUSHY_QUEST_LIST[2] . ' for Nina.',
      4 => 'Find a ' . $PLUSHY_QUEST_LIST[3] . ' for Nina.',
      5 => 'Find a ' . $PLUSHY_QUEST_LIST[4] . ' for Nina.',
      6 => 'Find a ' . $PLUSHY_QUEST_LIST[5] . ' for Nina.',
      7 => 'Find a ' . $PLUSHY_QUEST_LIST[6] . ' for Nina.',
      8 => 'Find a ' . $PLUSHY_QUEST_LIST[7] . ' for Nina.',
      9 => 'Find a ' . $PLUSHY_QUEST_LIST[8] . ' for Nina.',
      10 => 'And finally: find a ' . $PLUSHY_QUEST_LIST[9] . ' for Nina.',
    )
  ),
  'PatternActivation' => array(
    'title' => 'What Do You Think of My Labyrinth?',
    'description' => 'You found "Maze Piece", but aren\'t sure what to do with it.',
    'done_step' => 2,
    'steps' => array(
      1 => 'Ask Thaddeus (the Alchemist) about it.',
    )
  ),
  'TotemGardenActivation' => array(
    'title' => 'Low Resident on the Totem Pole',
    'description' => 'You found a totem, but realized you don\'t know the first thing about Totem Pole construction.',
    'done_step' => 2,
    'steps' => array(
      1 => 'Ask Nina about it.',
    ),
  ),
  'library quest' => array(
    'title' => 'Amaryllith! Amaryllith!',
    'description' => 'Marian has work for you at The Library...',
    'done_step' => 4,
    'steps' => array(
      1 => 'Collect some books to stock The Library with.',
      2 => 'Wait 24 hours.',
      3 => 'Bring unique samples of Hollow Earth literature and cuisine for Marian to sample.',
    ),
  ),
  'hephaestus charm' => array(
    'title' => 'In Search of a Hephaestus Charm',
    'description' => 'Acording to Matalie, Hephaestus Charms inspire pets to create great jewelry.',
    'done_step' => 3,
    'steps' => array(
      1 => 'Ask Lakisha about the Hephaestus Charm.',
      2 => 'Buy a Hephaestus Charm from Thaddeus.',
    )
  ),
  'close encounter' => array(
    'title' => 'Close Encounters of the Third Kind',
    'description' => 'Aliens have visited PsyPettia!',
    'done_step' => 7,
    'other_steps' => 'Finish your conversation with the FBI agents.',
    'steps' => array(
      1 => 'Find someone who will believe your story.',
    )
  ),
  'hidden cave quest' => array(
    'title' => 'Wellwaker',
    'description' => 'A mysterious map has lead you to a mysterious place...',
    'done_step' => 2,
    'steps' => array(
      1 => 'Find something to blow open the crack in the wall that the Mysterious Map leads you to.',
    )
  ),
);
?>
