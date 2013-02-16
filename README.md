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

* A web server (I mostly used Apache)
* PHP, with "short tags" enabled
* MySQL
* memcached (although you could probably edit it out of the code pretty easily)

Database and Other Settings
---------------------------

* configure libraries/settings.php
* configure commons/settings_light.php

unfortunately, PsyPets used two different libraries to connect to the database.
HandyDB is the newer, fancy method which uses better, faster functions; its
settings are in library/settings.php.  the code which uses the older functions
uses the settings in commons/settings_light.php  there are a lot of other
settings in commons/settings_light.php which you should be sure to configure,
as well, including domain name, cookies, and others.

also:

* commons/settings.php
    may have system-specific values you need to modify
* meta/privacy.php
    your privacy policy goes here (how do you use users' email addresses?) and
		be sure to note that the site uses cookies to maintain their sessions
* commons/tos.php
    should be edited to have the site's Terms of Service
* commons/abuseexamples.php
    references some rules in the ToS
* help/design-philosophies.php
    what direction do you wish to take the game with your future developments?
		for an example of what constitutes a design pillar, check out this article:
		http://diablo.incgamers.com/blog/comments/diablo-3s-seven-design-pillars-2

finally:

* check/update all the .htaccess files
* set up cron jobs for each of the tasks in crontab/
		
Create Database Tables
----------------------

you'll find everything in:

* db_structure.sql
* db_globals.sql

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

