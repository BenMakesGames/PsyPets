<?php
if($RESPEC !== true)
{
?>
<!--
     <h6>Personality</h6>
     <p>Describe the best thing about your pet's personality:</p>
     <ul class="plainlist">
      <li><input type="radio" name="personality" value="1" <?= $_POST['personality'] == 1 ? 'checked' : '' ?> id="pers1" /><label for="pers1"> Very extroverted</label></li>
      <li><input type="radio" name="personality" value="2" <?= $_POST['personality'] == 2 ? 'checked' : '' ?> id="pers2" /><label for="pers2"> Open to new ideas</label></li>
      <li><input type="radio" name="personality" value="3" <?= $_POST['personality'] == 3 ? 'checked' : '' ?> id="pers3" /><label for="pers3"> Is easy to get along with</label></li>
      <li><input type="radio" name="personality" value="4" <?= $_POST['personality'] == 4 ? 'checked' : '' ?> id="pers4" /><label for="pers4"> Doesn't leave things unfinished</label></li>
      <li><input type="radio" name="personality" value="5" <?= $_POST['personality'] == 5 ? 'checked' : '' ?> id="pers5" /><label for="pers5"> Is very playful</label></li>
      <li><input type="radio" name="personality" value="6" <?= $_POST['personality'] == 6 ? 'checked' : '' ?> id="pers6" /><label for="pers6"> Is independent</label></li>
     </ul>
-->
<?php
}
?>
     <h6>Physical and Mental Traits</h6>
     <p>Your pet is very...</p>
     <ul class="plainlist">
      <li><input type="radio" name="physical" value="1" <?= $_POST['physical'] == 1 ? 'checked' : '' ?> id="phys1" /><label for="phys1"> strong</label></li>
      <li><input type="radio" name="physical" value="2" <?= $_POST['physical'] == 2 ? 'checked' : '' ?> id="phys2" /><label for="phys2"> fast</label></li>
      <li><input type="radio" name="physical" value="3" <?= $_POST['physical'] == 3 ? 'checked' : '' ?> id="phys3" /><label for="phys3"> tough</label></li>
     </p>
     <p>... and...</p>
     <ul class="plainlist">
      <li><input type="radio" name="mental" value="1" <?= $_POST['mental'] == 1 ? 'checked' : '' ?> id="ment1" /><label for="ment1"> intelligent</label></li>
      <li><input type="radio" name="mental" value="2" <?= $_POST['mental'] == 2 ? 'checked' : '' ?> id="ment2" /><label for="ment2"> perceptive</label></li>
      <li><input type="radio" name="mental" value="3" <?= $_POST['mental'] == 3 ? 'checked' : '' ?> id="ment3" /><label for="ment3"> witty</label></li>
     </ul>
     <h6>Skills and Knowledges</h6>
     <p>Choose any two!</p>
     <ul class="plainlist">
      <li><input type="checkbox" name="skill[]" value="1" <?= ($_POST['skill'][0] == 1 || $_POST['skill'][1] == 1) ? 'checked' : '' ?> id="skill1" /><label for="skill1"> Martial arts</label></li>
      <li><input type="checkbox" name="skill[]" value="2" <?= ($_POST['skill'][0] == 2 || $_POST['skill'][1] == 2) ? 'checked' : '' ?> id="skill2" /><label for="skill2"> Athletic training</label></li>
      <li><input type="checkbox" name="skill[]" value="3" <?= ($_POST['skill'][0] == 3 || $_POST['skill'][1] == 3) ? 'checked' : '' ?> id="skill3" /><label for="skill3"> Stealth</label></li>
      <li><input type="checkbox" name="skill[]" value="4" <?= ($_POST['skill'][0] == 4 || $_POST['skill'][1] == 4) ? 'checked' : '' ?> id="skill4" /><label for="skill4"> Hunting</label></li>
      <li><input type="checkbox" name="skill[]" value="5" <?= ($_POST['skill'][0] == 5 || $_POST['skill'][1] == 5) ? 'checked' : '' ?> id="skill5" /><label for="skill5"> Gathering</label></li>
      <li><input type="checkbox" name="skill[]" value="6" <?= ($_POST['skill'][0] == 6 || $_POST['skill'][1] == 6) ? 'checked' : '' ?> id="skill6" /><label for="skill6"> Fishing</label></li>
      <li><input type="checkbox" name="skill[]" value="7" <?= ($_POST['skill'][0] == 7 || $_POST['skill'][1] == 7) ? 'checked' : '' ?> id="skill7" /><label for="skill7"> Mining</label></li>
<?php
if($SIGN_UP !== true)
{
?>
      <li><input type="checkbox" name="skill[]" value="9" <?= ($_POST['skill'][0] == 9 || $_POST['skill'][1] == 9) ? 'checked' : '' ?> id="skill9" /><label for="skill9"> Handicrafts</label></li>
      <li><input type="checkbox" name="skill[]" value="10" <?= ($_POST['skill'][0] == 10 || $_POST['skill'][1] == 10) ? 'checked' : '' ?> id="skill10" /><label for="skill10"> Painting</label></li>
      <li><input type="checkbox" name="skill[]" value="11" <?= ($_POST['skill'][0] == 11 || $_POST['skill'][1] == 11) ? 'checked' : '' ?> id="skill11" /><label for="skill11"> Carpentry</label></li>
      <li><input type="checkbox" name="skill[]" value="12" <?= ($_POST['skill'][0] == 12 || $_POST['skill'][1] == 12) ? 'checked' : '' ?> id="skill12" /><label for="skill12"> Jewelry-making</label></li>
      <li><input type="checkbox" name="skill[]" value="13" <?= ($_POST['skill'][0] == 13 || $_POST['skill'][1] == 13) ? 'checked' : '' ?> id="skill13" /><label for="skill13"> Sculpting</label></li>
      <li><input type="checkbox" name="skill[]" value="14" <?= ($_POST['skill'][0] == 14 || $_POST['skill'][1] == 14) ? 'checked' : '' ?> id="skill14" /><label for="skill14"> Electronics</label></li>
      <li><input type="checkbox" name="skill[]" value="15" <?= ($_POST['skill'][0] == 15 || $_POST['skill'][1] == 15) ? 'checked' : '' ?> id="skill15" /><label for="skill15"> Mechanics</label></li>
      <li><input type="checkbox" name="skill[]" value="16" <?= ($_POST['skill'][0] == 16 || $_POST['skill'][1] == 16) ? 'checked' : '' ?> id="skill16" /><label for="skill16"> Chemistry</label></li>
      <li><input type="checkbox" name="skill[]" value="17" <?= ($_POST['skill'][0] == 17 || $_POST['skill'][1] == 17) ? 'checked' : '' ?> id="skill17" /><label for="skill17"> Metal-working</label></li>
      <li><input type="checkbox" name="skill[]" value="18" <?= ($_POST['skill'][0] == 18 || $_POST['skill'][1] == 18) ? 'checked' : '' ?> id="skill18" /><label for="skill18"> Tailory</label></li>
      <li><input type="checkbox" name="skill[]" value="19" <?= ($_POST['skill'][0] == 19 || $_POST['skill'][1] == 19) ? 'checked' : '' ?> id="skill19" /><label for="skill19"> Magic-binding</label></li>
      <li><input type="checkbox" name="skill[]" value="20" <?= ($_POST['skill'][0] == 20 || $_POST['skill'][1] == 20) ? 'checked' : '' ?> id="skill20" /><label for="skill20"> Piloting</label></li>
      <li><input type="checkbox" name="skill[]" value="21" <?= ($_POST['skill'][0] == 21 || $_POST['skill'][1] == 21) ? 'checked' : '' ?> id="skill21" /><label for="skill21"> Astronomy</label></li>
      <li><input type="checkbox" name="skill[]" value="22" <?= ($_POST['skill'][0] == 22 || $_POST['skill'][1] == 22) ? 'checked' : '' ?> id="skill22" /><label for="skill22"> Music</label></li>
      <li><input type="checkbox" name="skill[]" value="23" <?= ($_POST['skill'][0] == 23 || $_POST['skill'][1] == 23) ? 'checked' : '' ?> id="skill23" /><label for="skill23"> Leather-working</label></li>
<?php
}
?>
     </ul>
     <h6>Personality</h6>
     <p>Choose the trait that best describes your pet's personality!</p>
     <ul>
      <li><input type="radio" name="personality" value="1" <?= $_POST['personality'] == 1 ? 'checked' : '' ?> id="person1" /><label for="person1"> Conscientious</label></li>
      <li><input type="radio" name="personality" value="2" <?= $_POST['personality'] == 2 ? 'checked' : '' ?> id="person2" /><label for="person2"> Laid-back</label></li>
      <li><input type="radio" name="personality" value="3" <?= $_POST['personality'] == 3 ? 'checked' : '' ?> id="person3" /><label for="person3"> Experimental</label></li>
      <li><input type="radio" name="personality" value="4" <?= $_POST['personality'] == 4 ? 'checked' : '' ?> id="person4" /><label for="person4"> Traditional</label></li>
      <li><input type="radio" name="personality" value="5" <?= $_POST['personality'] == 5 ? 'checked' : '' ?> id="person7" /><label for="person7"> Extroverted</label></li>
      <li><input type="radio" name="personality" value="6" <?= $_POST['personality'] == 6 ? 'checked' : '' ?> id="person8" /><label for="person8"> Introverted</label></li>
      <li><input type="radio" name="personality" value="7" <?= $_POST['personality'] == 7 ? 'checked' : '' ?> id="person9" /><label for="person9"> Playful</label></li>
      <li><input type="radio" name="personality" value="8" <?= $_POST['personality'] == 8 ? 'checked' : '' ?> id="person10" /><label for="person10"> Serious</label></li>
      <li><input type="radio" name="personality" value="9" <?= $_POST['personality'] == 9 ? 'checked' : '' ?> id="person11" /><label for="person11"> Independent</label></li>
      <li><input type="radio" name="personality" value="10" <?= $_POST['personality'] == 10 ? 'checked' : '' ?> id="person12" /><label for="person12"> Obedient</label></li>
     </ul>
<?php
if($SIGN_UP !== true)
{
?>
     <h6>Pet Advantages</h6>
     <?= $ADVANTAGES_DESC ?>
     <ul class="plainlist">
      <li><input type="checkbox" name="102" id="s102" <?= ($_POST['102'] == 'on' ? 'checked' : '') . $ADVANTAGES_DISABLED ?> /> <label for="s102">Acute senses</label></li>
      <li><input type="checkbox" name="109" id="s109" <?= ($_POST['109'] == 'on' ? 'checked' : '') . $ADVANTAGES_DISABLED ?> /> <label for="s109">Berserker</label></li>
      <li><input type="checkbox" name="111" id="s111" <?= ($_POST['111'] == 'on' ? 'checked' : '') . $ADVANTAGES_DISABLED ?> /> <label for="s111">Bottomless stomach</label></li>
      <li><input type="checkbox" name="103" id="s103" <?= ($_POST['103'] == 'on' ? 'checked' : '') . $ADVANTAGES_DISABLED ?> /> <label for="s103">Cat-like balance</label></li>
      <li><input type="checkbox" name="112" id="s112" <?= ($_POST['112'] == 'on' ? 'checked' : '') . $ADVANTAGES_DISABLED ?> /> <label for="s112">Handy</label></li>
      <li><input type="checkbox" name="105" id="s105" <?= ($_POST['105'] == 'on' ? 'checked' : '') . $ADVANTAGES_DISABLED ?> /> <label for="s105">Lightning calculator</label></li>
      <li><input type="checkbox" name="101" id="s101" <?= ($_POST['101'] == 'on' ? 'checked' : '') . $ADVANTAGES_DISABLED ?> /> <label for="s101">Light sleeper</label></li>
      <li><input type="checkbox" name="107" id="s107" <?= ($_POST['107'] == 'on' ? 'checked' : '') . $ADVANTAGES_DISABLED ?> /> <label for="s107">Luck of the fae</label></li>
      <li><input type="checkbox" name="108" id="s108" <?= ($_POST['108'] == 'on' ? 'checked' : '') . $ADVANTAGES_DISABLED ?> /> <label for="s108">Medium</label></li>
      <li><input type="checkbox" name="110" id="s110" <?= ($_POST['110'] == 'on' ? 'checked' : '') . $ADVANTAGES_DISABLED ?> /> <label for="s110">Predicts earthquakes</label></li>
      <li><input type="checkbox" name="114" id="s114" <?= ($_POST['114'] == 'on' ? 'checked' : '') . $ADVANTAGES_DISABLED ?> /> <label for="s114">Pruriency</label></li>
      <li><input type="checkbox" name="106" id="s106" <?= ($_POST['106'] == 'on' ? 'checked' : '') . $ADVANTAGES_DISABLED ?> /> <label for="s106">Silver tongue</label></li>
      <li><input type="checkbox" name="115" id="s115" <?= ($_POST['115'] == 'on' ? 'checked' : '') . $ADVANTAGES_DISABLED ?> /> <label for="s115">Sleep-walker</label></li>
      <li><input type="checkbox" name="113" id="s113" <?= ($_POST['113'] == 'on' ? 'checked' : '') . $ADVANTAGES_DISABLED ?> /> <label for="s113">Star student</label></li>
      <li><input type="checkbox" name="100" id="s100" <?= ($_POST['100'] == 'on' ? 'checked' : '') . $ADVANTAGES_DISABLED ?> /> <label for="s100">Steady hands</label></li>
      <li><input type="checkbox" name="104" id="s104" <?= ($_POST['104'] == 'on' ? 'checked' : '') . $ADVANTAGES_DISABLED ?> /> <label for="s104">Tough hide</label></li>
     </ul>
<?php
}
?>
