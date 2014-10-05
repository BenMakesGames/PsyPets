<?php
class PetQuestGardening extends PetQuest
{
    public static $POSSIBLE_PRODUCE = array(
        'Tomato' => array('hoursRequired' => 32, 'quantityFraction' => 9), // is 32 ~3 days? if not, adjust to be ~3 days
        'Redsberries' => array('hoursRequired' => 28, 'quantityFraction' => 6),
        'Blueberries' => array('hoursRequired' => 28, 'quantityFraction' => 7), // double-check: which does the encyclopedia say is less common?
        // etc
    );

    // @TODO: something extra for having farm add-on
    //   more produce options available?
    //   greater quantity?
    //   something else??

    protected function Init($args)
    {
        global $now;

        $month = date('n', $now);

        // @TODO: lean toward growing something that is one of the involved pets' favorite foods
        // @TODO: pick produce according to time of year ($month)

        $produce = array_rand(self::$POSSIBLE_PRODUCE);

        return array(
            'workPerformed' => 0,
            'hoursWorked' => 0,
            'produce' => $produce,
        );
    }

    public function Work()
    {
        // always increase hours worked by 1, regardless of number of pets
        // project is complete when hoursworked >= $POSSIBLE_PRODUCE[$produce]
        // more workPerformed == greater yield

        // @TODO: random chance that some random an/or silly event happens
        //   look to old harvest moon for inspirations

        $produceName = $this->questProgressData['produce'];
        $produceMeta = self::$POSSIBLE_PRODUCE[$produceName];

        if($this->questProgressData['hoursWorked'] >= $produceMeta['hoursRequired'])
        {
            // "quantityFraction" assumes each pet put in 3 points/hour; that is probably a minimum.
            // after dividing by this fraction, take the log, and then multiply by 10.
            $quantity = log($this->questProgressData['workPerformed'] / $produceMeta['quantityFraction'], 10) * 10;

            // @TODO: add $quantity of $produceName
            // divide produce evenly between pets involved, rounding down
            $quantityPerPet = floor($quantity / count($this->participantIds));
            $totalQuantity = $quantityPerPet * count($this->participantIds);
            // create journal entry
        }
    }
}