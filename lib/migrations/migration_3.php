<?php
class Migration_3
{
    public function Up()
    {
        fetch_none(
            "INSERT INTO `monster_items` (`idnum`, `implementationtime`, `itemname`, `itemtype`, `anagramname`, `bigname`, `custom`, `can_pawn_with`, `can_pawn_for`, `playdesc`, `bulk`, `weight`, `flammability`, `graphictype`, `graphic`, `recycle_for`, `can_recycle`, `recycle_fraction`, `additional_flavors`, `durability`, `value`, `key_id`, `is_equipment`, `equipeffect`, `equipreqs`, `equip_is_revised`, `req_str`, `req_dex`, `req_athletics`, `req_sta`, `req_int`, `req_per`, `req_wit`, `equip_open`, `equip_extraverted`, `equip_conscientious`, `equip_playful`, `equip_independent`, `equip_str`, `equip_dex`, `equip_sta`, `equip_int`, `equip_per`, `equip_wit`, `equip_mining`, `equip_lumberjacking`, `equip_fishing`, `equip_painting`, `equip_sculpting`, `equip_carpentry`, `equip_jeweling`, `equip_electronics`, `equip_mechanics`, `equip_adventuring`, `equip_hunting`, `equip_gathering`, `equip_smithing`, `equip_tailoring`, `equip_leather`, `equip_crafting`, `equip_binding`, `equip_chemistry`, `equip_piloting`, `equip_gardening`, `equip_stealth`, `equip_athletics`, `equip_fertility`, `equip_goldenmushroom`, `equip_vampire_slayer`, `equip_berry_craft`, `equip_were_killer`, `equip_pressurized`, `equip_flight`, `equip_fire_immunity`, `equip_chill_touch`, `equip_healing`, `equip_more_dreams`, `equipreincarnateonly`, `equipl33tonly`, `is_grocery`, `is_edible`, `max_conscientiousness`, `ediblehealing`, `ediblecaffeine`, `edibleenergy`, `ediblefood`, `ediblesafety`, `ediblelove`, `edibleesteem`, `playbed`, `playfood`, `playsafety`, `playlove`, `playesteem`, `playstat`, `hourlyfood`, `hourlysafety`, `hourlylove`, `hourlyesteem`, `hourlystat`, `book_text`, `action`, `enc_entry`, `treasurevalue`, `rare`, `treasure`, `nomarket`, `noexchange`, `nosellback`, `cursed`, `cancombine`, `norepair`, `questitem`, `admin_notes`) VALUES " .
            "(NULL, 1412053523, 'Floral-print Pillow', 'craft/furniture/pillow', 'afiillllnoopprrtw', 'no', 'no', 'yes', 'no', '', 40, 30, 0, 'bitmap', 'pillow_floral.png', 'Fluff,Fluff,Fluff,Black Dye,Black Dye', 'yes', 1, '', 0, 38, 0, 'no', '', '', 'yes', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'no', 'no', 'no', 'no', 'no', 'no', 'no', 'no', 'no', 'no', 'no', 'no', 'no', 'no', 0, 0, 0, 0, 0, 0, 0, 0, 'no', 0, 0, 0, 0, '', 0, 1, 0, 1, '', '', '', '', 0, 'no', 'no', 'no', 'no', 'no', 'no', 'yes', 'no', 'no', '');"
        );

        fetch_none(
            "INSERT INTO `psypets`.`psypets_tailors` (`idnum`, `difficulty`, `complexity`, `priority`, `ingredients`, `makes`, `mazeable`, `min_month`, `max_month`, `min_openness`, `max_openness`, `min_music`, `min_astronomy`, `min_playful`, `max_playful`, `is_secret`, `is_berries`, `is_burny`) VALUES " .
            "(NULL, '3', '3', '120', 'Small White Pillow,Floral Design', 'Floral-print Pillow', 'yes', '1', '12', '3', '10', '0', '0', '3', '10', 'no', 'no', 'no');"
        );

        fetch_none(
            "INSERT INTO `psypets`.`psypets_paintings` (`idnum`, `difficulty`, `complexity`, `priority`, `ingredients`, `makes`, `mazeable`, `addon`, `min_month`, `max_month`, `min_openness`, `max_openness`, `min_music`, `solo`, `min_astronomy`, `min_playful`, `max_playful`, `is_secret`) VALUES " .
            "(NULL, '3', '3', '120', 'Small White Pillow,Floral Design', 'Floral-print Pillow', 'yes', 'no', '1', '12', '3', '10', '0', 'no', '0', '3', '10', 'no');"
        );
    }

    public function Down()
    {
        throw new Exception('Can\'t migrate down.');
    }
}
