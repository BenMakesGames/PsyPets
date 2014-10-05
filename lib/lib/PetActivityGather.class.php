<?php
class PetActivityGather extends PetActivity
{
    public function Work()
    {
        // @TODO: rare chance of something totally different happening

        // total up pets' skills
        // acquire an amount of food hours based on this total

        $foodHours = 0;
        foreach($this->pets as $pet)
        {
            $foodHours += $pet[''];
        }
        $foodHours = log($foodHours, 10) * 8; // less food than hunting

        // feed each pet evenly, rounding up
        $foodHoursPerPet = ceil($foodHours / count($this->pets));

        foreach($this->pets as $pet)
        {
            // unlike hunting, no additional energy loss; only a small chance of a tiny wound

            if($foodHoursPerPet + $pet['food'] >= max_food($pet))
            {
                $excessFood = $foodHoursPerPet = max_food($pet) - $pet['food'];
                // @TODO find closest appropriate food item, and give to owner
            }

            // each pet has a chance of bringing home some extra gathering item... leaves (edible or not), reed, rocks, etc
        }
    }
}
