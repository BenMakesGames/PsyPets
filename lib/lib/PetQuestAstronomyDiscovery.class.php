<?php
class PetQuestAstronomyDiscovery extends PetQuest
{
    /*
        questProgressData = {
            workPerformed: 0,
            hoursSearched: 0
        }
    */
    protected function Init($args)
    {
        return array(
            'workPerformed' => 0,
            'hoursSearched' => 0,
        );
    }

    public function Work()
    {
        // requires an astronomy picture to get started; consumes the picture
        // for every hour and every pet involved, add 1 to hoursSearched, and
        // total of pets' relevant skills to workPerformed

        // chance to complete project = hoursSearched %

        // depending on workPerformed, make a discovery and produce a cool photo
        // always a chance that nothing is found, regardless of skill, but greater
        // skill == greater chance to find SOMETHING
    }
}