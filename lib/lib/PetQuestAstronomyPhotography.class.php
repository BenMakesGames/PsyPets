<?php
class PetQuestAstronomyPhotography extends PetQuest
{
    /*
        questProgressData = {
            workPerformed: 1
        }
    */
    protected function Init($args)
    {
        return array(
            'workPerformed' => 0,
        );
    }

    public function Work()
    {
        // @TODO: work on "progress"

        // work toward progress is log(total of pets involved)
        // produces a picture item; one per participating pet
    }
}