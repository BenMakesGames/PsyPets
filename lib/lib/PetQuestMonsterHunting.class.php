<?php
class PetQuestMonsterHunting extends PetQuest
{
    protected function Init($args)
    {
        global $now;

        $month = date('n', $now);
        $difficulty = $args['difficulty'];

        return array(
            'state' => 'gatheringInformation',
            'informationRequired' => 0,
            'trackingRequired' => 0,
            'monster' => array(
                'stealth' => 0,
                'speed' => 0,
                'strength' => 0,
                'health' => 0,
            )
        );
    }

    public function Work()
    {
        switch($this->questProgressData['state'])
        {
            case 'gatheringInformation': $this->WorkGatheringInformation(); break;
            case 'trailing': $this->WorkTrailing(); break;
            case 'fighting': $this->WorkFighting(); break;
        }
    }

    protected function WorkGatheringInformation()
    {
        // pets consult maps, visit the library, or ask around town to
        // determine the location of their target
    }

    protected function WorkTrailing()
    {
        // pets using nature, tracking, sneaking skills to locate the monster
    }

    protected function WorkFighting()
    {
        // sometimes, the monster escapes before it can be finished off,
        // and the pets must resume "trailing", however trailing is
        // much easier this time, and the monster is already wounded
    }
}
