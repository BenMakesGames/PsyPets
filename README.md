**hey, listen!** this code is super-old (you probably noticed). I have fond memories of it, and am proud of what I built, but wow, I've learned a lot since then. today, I'd say PsyPets is an interesting example of how some web sites were made, in the days of IE6, before package managers and smart phones, but probably isn't super-relevant today. also, the code is rife with problems, and bad decisions :P

I have a new pet game, [Poppy Seed Pets](https://poppyseedpets.com), which uses modern tools, and better software and game-design practices. If you like pet games, check it out! :)

<a href='https://ko-fi.com/A0A12KQ16' target='_blank'><img height='36' style='border:0px;height:36px;' src='https://cdn.ko-fi.com/cdn/kofi3.png?v=3' border='0' alt='Buy Me a Coffee at ko-fi.com' /></a>

PsyPets
=======

your very own pet game, based on psychology!

License
-------

PsyPets, Copyright (C) 2004-2013 Ben Hendel-Doying [http://www.telkoth.net/]

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do
so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

Server Requirements
-------------------

* A web server (I've only worked with Apache/httpd)
* PHP, with "short tags" enabled
* MySQL
* memcache(d) (although you could probably edit it out of the code pretty easily)

PHP no longer recommends the use of "short tags", and some hosts and installations disable it by default.
sorry: PsyPets is way-old, and uses some funny old things like this :P you can enable short tags by
finding the php.ini file on your server, and editing it. search the document for "short tags" or
"short_tags" or something like that... you should find the line which says that they're off... edit that
line to turn them on! (you may need to restart Apache/httpd/whatever after making changes to php.ini - 'depends
on your exact configuration.)

Installing Required Software
----------------------------

www.psypets.net runs on Linux. I have, at least at one time in the past, installed PsyPets on a Windows machine, but
it was long ago, and I did not test it extensively. if you plan to install on Windows, you may encounter trouble.

the install procedures will vary a lot depending on the exact OS you use. also keep in mind that not all Linuxes are alike (even basic tasks like installing software will vary from Linux to Linux).

* if you have a host that installs software for you, you probably don't need to worry about anything, except that memcache(d) will probably not be installed; you'll need to ask for it to be installed, or edit it out of PsyPets.
* if you are setting up your own Linux host, you probably don't need me to tell you how :P note that installing memcache(d) is a pain in the ass - google it.
* if you want to set up PsyPets on your personal Windows PC, you should google for "WAMP" (a Windows Apache MySQL PHP bundle) and install one. most WAMP bundles come with lovely tools that make editing Apache, MySQL and PHP settings easy. memcache(d) may not be available for Windows - I haven't looked.
* if you want to set up PsyPets on your personal Mac PC, you're on your own. (I haven't messed with Macs since the days of HyperCard and ResEdit.) but since Mac OS X is a special flavor of Linux, it should work well (with fewer problems than you'd get on Windows). memcache(d) may still be a problem.

additional tools I have personally used for development and maintainance of PsyPets:

* Notepad++ - you could also try PHPStorm: I use it at work, but never used it for PsyPets.
* phpMyAdmin
* Adobe Fireworks - for making graphics. it's amazing. I've used it for over 10 years now (god, that's terrifying to think about >_>)
* FileZilla - for uploading to a remote server.
* PuTTy - for command-line access to a remote server.

Database and Other Settings
---------------------------

to start, edit these two files to get things running:

* libraries/settings.php
* commons/settings_light.php

unfortunately, PsyPets used two different libraries to connect to the database.
HandyDB is the newer, fancy method which uses better, faster functions; its
settings are in library/settings.php.  the code which uses the older functions
uses the settings in commons/settings_light.php  there are a lot of other
settings in commons/settings_light.php which you should be sure to configure,
as well, including domain name, cookies, and others.

also:

* commons/settings.php
		may have system-specific variables you need to modifiy
* meta/privacy.php
    your privacy policy goes here (how do you use users' email addresses?) and
		be sure to note that the site uses cookies to maintain their sessions
* commons/tos.php
    should be edited to have the site's Terms of Service
* commons/abuseexamples.php
    references some rules in the ToS
* help/design-philosophies.php
    what direction do you wish to take the game with your future developments?
		for some examples of design pillars, and how they're used in game design, check out these articles:
		http://massively.joystiq.com/2013/03/29/gdc-2013-dean-hall-on-the-pillars-of-dayzs-design/
		http://www.destinypedia.com/Seven_Pillars_of_Design
		http://danielprimed.com/2010/07/design-discussions-deconstructing-the-video-game-three-pillars-of-design-for-interactive-drama-heavy-rain/
		http://diablo.incgamers.com/blog/comments/diablo-3s-seven-design-pillars-2

finally:

* check/update all the .htaccess files
* set up cron jobs for each of the tasks in crontab/
    if you don't know what cron jobs are, search the internets!
		if you're hosting on Windows, it won't be cron jobs, it'll be scheduled tasks
		
Create Database Tables
----------------------

you'll find everything in:

* db_structure.sql
* db_globals.sql

you can import these via phpMyAdmin (see "installing software", above), or the
command-line, if you know how.

Sign Up; Give Yourself Administrative Rights
--------------------------------------------

once you've got things working, sign up and log in. if you're having trouble
activating your account, you can manually activate it by modifying your user
data in the monster_users table.

to give yourself admin rights, create an entry in the monster_admins table.
some of the fields are very old and no longer used, but the field names are
hopefully self-explanatory.

test out your administrative powers at www.YOURSITE.COM/admin/tools.php

Create... EVERYTHING
--------------------

you will need to upload your own graphics, and create all of the items and
recipes which appear in the game.

a COUPLE of the admin tools provide interfaces for this, but even some of those
are old and do not work properly. it is probably best to edit things in the
database directly (I recommend installing phpMyAdmin!)

some database tables to get started on:

* monster_items
    contains all of the game's item definitions
* monster_monster and monster_prey
    contains the monsters and prey which your pets may adventure/hunt
* monster_recipes
    contains recipes which players can prepare
* psypets_jewelry (and many others)
    contains crafting information for the pets

Table Descriptions
------------------

<pre>
bay_spam                    used by bayesian troll detector
bay_totals                  used by bayesian troll detector
monster_admins              give an account administrative right by creating an entry in this table
monster_auctions            running auction house auctions
monster_events              park events, active and past
monster_globals             holds a handful of globals. if you feel like improving the game, move this data into PHP associative arrays :P
monster_gods                the mood of the gods, based on donations; evaluated daily by cron jobs
monster_graphics            graphics copyright information
monster_houses              player house data (size, addons, rooms, etc)
monster_inventory           player inventory
monster_items               item definitions for the game. when someone has an "Orange" in their inventory, its properties are that of the "Orange" defined in this table
monster_loginhistory        player login history; trimmed daily by cron jobs
monster_mail                in-game messages
monster_monsters            monsters your pet can defeat; prizes are of the format "400|Orange,200|Apple", which means a 40% chance of dropping an orange, and a 20% chance of dropping an apple. "needs_key" matches "key_id" in the items table
monster_passreset           password reset requests
monster_petlogs             pet logs; trimmed daily by cron jobs
monster_pets                pet data
monster_plaza               plaza sections. a section name prefixed with a "#" will be displayed as a heading, and is not an actual section (ex: "#Out-of-Game Chat"). "admins" is a comma-separated list of account id numbers which are watchers for that plaza section
monster_posts               plaza posts
monster_prey                prey your pet can hunt; see monster_monsters description for details
monster_profiles            account searchable profiles
monster_projects            projects your pets are currently working on (visible at My House)
monster_recipes             recipes players can prepare. ex: "ingredients" and "makes" fields are comma-separated item names (ex: "Orange,Sugar,Tea Leaves")
monster_reports             I think this is an old table for abuse reports made by players (see psypets_abusereports below), but I'm not 100% sure >_>
monster_smith               smith, tailory, and alchemy exchanges. supplies are comma-separated item names, like recipes, however "makes" can only be ONE item. "secret" recipes are supported only by the smithy, and are shown only when you have the needed items in your storage already
monster_statistics          daily statistics, generated by cron jobs
monster_threads             plaza threads
monster_trades              trades in-progress, and full history of trades
monster_transactions        bank history
monster_users               user data. mark NPC accounts with the "is_npc" flag, which prevents login to that account.
monster_watchermove         a watcher thread-move request
monster_watching            player "favorite" threads
psypets_404_log             a log of 404s that players encounter
psypets_abusereports        abuse reports made by players
psypets_adventure           daily adventures that players are on
psypets_advertising         running in-game ads, cleaned up daily by cron jobs
psypets_airships            airships (a currently-broken feature)
psypets_alchemy             alchemy recipes in the tower. items_in is in a format like this: "2|Apple,1|Orange" meaning you use 2 apples and an orange for the transformation. item_out must be only a single item name (like monster_smith table) ex "Topaz". see addons/tower scripts to see how 'month' and 'type' are used
psypets_apiaries            apiary addons
psypets_aquariums           aquarium addons
psypets_arcadegames         arcade games pets can "defeat"; see monster_monsters table for details on how to create "prizes"
psypets_ark                 pet ark
psypets_auctions            unused, new auction system table. safe to delete.
psypets_auction_bids        unused, new auction system table. safe to delete.
psypets_autosort            player autosort info
psypets_badges              player badges. see also commons/badgelib.php 
psypets_basement            basements
psypets_bindings            magic binding crafts projects for pets; see general information about crafts below
psypets_botreport           bot report; broken feature; can probably delete, but search code for references to this table
psypets_cardgame            used for the mario 3-esque memory card game (from Magic Cards). safe to delete, unless you want to recreate that item.
psypets_carpentry           carpentry crafts projects for pets; see general information about crafts below
psypets_changelog           changelog
psypets_chemistry           chemistry crafts projects for pets; see general information about crafts below
psypets_civilizations       for multiverse addon; incomplete; safe to delete, unless you wish to develop it yourself
psypets_crafts              handicrafts crafts projects for pets; see general information about crafts below
psypets_dailychallenge      more daily adventure info
psypets_daily_report_stats  I believe this is unused and safe to delete; search code for references to make sure
psypets_dreidel_logs        for the dreidel monthly item. safe to delete, unless you want to recreate this item
psypets_dungeons            dungeon addons
psypets_failedlogins        failed login attempts (if someone fails to log in too many times, log attempts are disabled for a short time)
psypets_farms               farm addons
psypets_fireplaces          fireplace addons
psypets_fireplace_log       fireplace addon logs
psypets_flash_messages      messages to be displayed to player at next opportunity (to be clear, this is not for you to manually add entries to; the game uses this internally itself)
psypets_friendreport        "someone friended you" logs
psypets_galactic_objects    for multiverse addon; incomplete; safe to delete, unless you wish to develop it yourself
psypets_gamesold            logs of sellback/throw out/pawn actions that players take; used by an admin tool to report how often people are getting rid of certain items
psypets_game_rooms          game room addons
psypets_game_room_games     game room addon info
psypets_gardening           gardening projects; "ingredients" and "makes" field use same format as monster_recipes table
psypets_graveyard           dead pets
psypets_groupboxlogs        
psypets_groups
psypets_group_currencies
psypets_group_invites
psypets_group_pet_currencies
psypets_group_player_currencies
psypets_group_ranks
psypets_homeimprovement     list of add-ons players may build. "requirement" is the difficulty/time requirement (10-20 is really use; 100 is super-hard, like the Colossus add-on); "craft_reqs" is a comma-separated list of items needed, like monster_recipes
psypets_ideachart           for game idea voting
psypets_ideachart_complete
psypets_ideachart_tags
psypets_ideavotes
psypets_inventions          electrical engineering crafts projects for pets; see general information about crafts below
psypets_item_sales          I believe this is unused and safe to delete; search code for references
psypets_jewelry             jewelry projects for pets; see general information about crafts below
psypets_known_recipes       for kitchen add-on
psypets_lakes               lake addons
psypets_leatherworks        leather crafts projects for pets; see general information about crafts below
psypets_libraries
psypets_locations           locations pets can gather food. see monster_monsters and monster_prey for how to use "prizes" and "needs_key"
psypets_maprooms            map room add-on
psypets_maze                pattern information
psypets_maze_gates
psypets_maze_messages
psypets_mechanics           mechanical engineering crafts projects for pets; see general information about crafts below
psypets_monkeylog           tower monkey logs
psypets_museum
psypets_museum_displays
psypets_news                city hall news. gets duplicated to the plaza, which is inefficient. if you feel like doing some work, trash this table and edit the city hall and front page to pull from the plaza, instead
psypets_notes               player notebook notes
psypets_nuclear_power_plants unused; safe to delete
psypets_overbuy_report      probably unused and safe to delete; search code for references
psypets_paintings           painting crafts projects for pets; see general information about crafts below
psypets_park_event_results
psypets_pawned_for          logs on how many times an item is pawned for; used by admin tools to show how wanted items are
psypets_petbadges
psypets_petlives            pet reincarnation logs
psypets_petstats            pet statistics
psypets_pet_extra_stats     more pet statistics
psypets_pet_level_logs      pet affection-up log
psypets_pet_market
psypets_pet_relationships
psypets_planets             that multiverse add-on again
psypets_player_stats        player statistics
psypets_polls               past polls and active poll
psypets_poll_votes
psypets_possible_trolling
psypets_post_notification
psypets_post_thumbs         thumbs up/down made by players
psypets_profilecomments
psypets_profile_pet         pet profile text
psypets_profile_text        player profile text
psypets_profile_treasures   player profile item preferences
psypets_profile_user        I believe this is unused and safe to delete; double-check in the code, as always
psypets_publictrades
psypets_questvalues         values kept track of for quests and other purposes are kept here
psypets_reversemarket       seller's market info
psypets_sculptures          sculpture crafts projects for pets; see general information about crafts below
psypets_sellback_report     gamesold items are logged here; used by admin tools to show which items people are just gameselling (to help identify which items could have more-better uses)
psypets_shrines             shrine add-ons
psypets_sidewalks           sidewalk add-ons
psypets_slots               for slot-machine monthly item; safe to delete unless you intend to recreate this item
psypets_smiths              smithing crafts projects for pets; see general information about crafts below
psypets_starlog             "a post you made has received gold stars" log
psypets_stars               multiverse add-on info again
psypets_stellar_objects     multiverse add-on info... AGAIN
psypets_store_portraits     pictures people have drawn for their stores
psypets_stpatricks          st. patrick's day event info
psypets_tailors             tailory crafts projects for pets; see general information about crafts below
psypets_threadpolls         never implemented; safe to delete (but search code to be double-sure)
psypets_thread_history      history of watcher actions taken on threads (in case a watcher is misbehaving >_>)
psypets_totempoles
psypets_towers              tower addons
psypets_towns               group "town"s
psypets_trading_house_bids
psypets_trading_house_requests
psypets_universes           multiverse add-on
psypets_universe_history    multiverse add-on
psypets_user_enemies        player block lists
psypets_user_friends        player friend lists
psypets_warninglog          admin tool to keep track of player offenses. 100% private to admin, just to help you keep track of player behavior. (if you feel something is worth writing here, it's probably worth talking to the player!)
psypets_watchedthreads      "favorited" threads... (are there two tables for this? maybe one is unused? not sure what's going on here...)
psypets_wired               virtual hide and go seek tag info
psypets_zoos                menagerie addon
</pre>

Crafting Tables
---------------

crafting's a big part of the pet activities. here's a breakdown of every field used in the various crafting tables (ex: psypets_jewelry):

* idnum - just the id; let the DB assign these (and actually, that's true of all tables: DO NOT TOUCH idnums! just let MySQL handle them.)
* difficulty - the difficulty to make. I used 1-25 (1 as easy, 20 as hard, 25 as mastery level)
* complexity - the time it takes. I tended to make this close to the difficulty level at low difficulties, and as much as twice as high for harder crafts
* priority - when a pet finds multiple projects it can make, it sorts the list by priority, and tries the higher-priority ones first. I used this to make crafts like Cloth more common, since it is used in more crafts. I recommend setting the VAST MAJORITY of projects to the default of 120, and only setting a different priority for those occasional projects which you want to be more or less common.
* ingredients - comma-separated items needed for the project, ex: "Copper,Copper,Iron,Red Dye"
* makes - the single item created, ex: "Awesome Axe of Awesomeness" (doesn't support comma-separated lists of items to make - but you should add support for that! it'd be cool!)
* mazeable - unused (used to specify whether the item could appear in the pattern, but now the pattern just searches people's stores to find items for sale)
* addon - whether or not the resulting craft is used to make house add-ons; pets give these projects priority when the house is full
* min_month and max_month - the range of months the item is available; 1 = January, 12 = December. if you want a range to cross years (ex: winter-only), you need two separate entries! lame.
* min_/max_ stats - the range of stats required by the pet to make this item. for personality stats, it's not a strict requirement, but pets that fall outside the range are less likely to make the item
* is_secret - if a pet project is marked as secret, it will never show up in the pet logs when a pet fails to make it
* is_berries/burny/etc - whether or not the project has that special quality. equipment and special pet abilities can give bonuses when crafting items with these special traits.
