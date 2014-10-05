<?php
class PetActivityHunt extends PetActivity
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
        $foodHours = log($foodHours, 10) * 10;

        // feed each pet evenly, rounding up
        $foodHoursPerPet = ceil($foodHours / count($this->pets));

        foreach($this->pets as $pet)
        {
            // pets get tired, and possibly a small wound

            if($foodHoursPerPet + $pet['food'] >= max_food($pet))
            {
                $excessFood = $foodHoursPerPet = max_food($pet) - $pet['food'];
                // @TODO find closest appropriate food item, and give to owner
            }

            // each pet has a chance of bringing home some extra hunting item... leather, fluff, feathers, etc
        }
    }
}