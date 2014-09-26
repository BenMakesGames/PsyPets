<?php
if($okay_to_be_here !== true)
    exit();

$now = time();

$deletion = array();
$messages = array();

$minipalms = $database->FetchMultiple('SELECT data,idnum FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Minipalm\' AND location=' . quote_smart($this_inventory['location']));

$num_minipalms = count($minipalms);

if($num_minipalms == 0)
    echo 'The ' . $this_inventory['itemname'] . ' looks around, but seeing nothing that catches its interest, powers down again.';
else
{

    foreach($minipalms as $minipalm)
    {
        $data = $minipalm['data'];
        $idnum = $minipalm['idnum'];
        $day = 60 * 60 * 24;

        $coconut = false;
        $dies = false;

        if(strlen($data) == 0)
        {
            $data = $now + $day;

            fetch_none('UPDATE monster_inventory SET data=' . quote_smart($data) . ' WHERE idnum=' . (int)$idnum . ' LIMIT 1');
        }
        else if($now > $data)
        {
            if(rand(1, 8) == 1)
            {
                $dies = true;
                $deletion[] = $idnum;
            }
            else
            {
                $data = $now + rand($day, $day * 3);
                $coconut = true;

                fetch_none('UPDATE monster_inventory SET data=' . quote_smart($data) . ' WHERE idnum=' . (int)$idnum . ' LIMIT 1');
            }
        }

        if(!$dies)
        {
            if($coconut)
                $coconuts++;
            else
                $nothing++;
        }
    }

    if($coconuts > 0)
    {
        add_inventory_quantity($user['user'], '', 'Coconut', 'Shaken out of a Minipalm by your ' . $this_inventory['itemname'], $this_inventory['location'], $coconuts);

        $messages[] = 'shaking loose ' . say_number($coconuts) . ' Coconut' . ($coconuts != 1 ? 's' : '');

        $punctuate = '.';

        $RECOUNT_INVENTORY = true;
    }

    $num_deleted = count($deletion);

    if($num_deleted > 0)
    {
        fetch_none('DELETE FROM monster_inventory WHERE idnum IN (' . implode(', ', $deletion) . ') LIMIT ' . $num_deleted);

        add_inventory_quantity($user['user'], '', 'Log', 'The remains of a Minipalm after having been eaten by your ' . $this_inventory['itemname'], $this_inventory['location'], $num_deleted);

        $plural = ($num_deleted != 1 ? 's' : '');

        $messages[] = 'eating ' . say_number($num_deleted) . ' Minipalm' . $plural . ' (leaving behind the Log' . $plural . ')';

        $punctuate = '!';

        $RECOUNT_INVENTORY = true;
    }

    if(count($messages) > 0)
        $message = 'The ' . $this_inventory['itemname'] . ' zips around the room, ' . implode(' and ', $messages) . $punctuate . '  The remaining ' . say_number($nothing) . ' were apparently uninteresting, as they remained untouched.';
    else
        $message = 'The ' . $this_inventory['itemname'] . ' zips around the room for a little before finally powering down without having touched ' . ($num_minipalms > 0 ? 'any of the Minipalms.' : 'the Minipalm.');

    echo $message;
}
