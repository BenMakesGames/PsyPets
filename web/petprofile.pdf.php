<?php
$require_login = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/grammar.php';
require_once 'commons/utility.php';
require_once 'commons/petlib.php';
require_once 'commons/zodiac.php';
require_once 'commons/petgraphics.php';

require_once 'commons/fpdf/fpdf.php';
require_once 'commons/fpdf/betterfpdf.php';

$petid = (int)$_GET['petid'];

$this_pet = $database->FetchSingle('SELECT * FROM `monster_pets` WHERE idnum=' . $petid . ' LIMIT 1');

if($this_pet === false)
{
  header('Location: /directory.php');
  exit();
}

$pet_age = PetAge($this_pet['birthday'], $now);
$pet_years = PetYears($this_pet['birthday'], $now);

$special_abilities = array();

if($this_pet['special_firebreathing'] == 'yes')
  $special_abilities[] = 'Fire-breathing';

if($this_pet['special_chameleon'] == 'yes')
  $special_abilities[] = 'Chameleon skin';

if($this_pet['special_sparkles'] == 'yes')
  $special_abilities[] = 'Sparkles';

if($this_pet['special_digital'] == 'yes')
  $special_abilities[] = 'Dreams in Digital';

if($this_pet['special_love'] == 'yes')
  $special_abilities[] = 'Doki-doki';

if($this_pet['special_lightning'] == 'yes')
  $special_abilities[] = 'Lightning';

if($this_pet['merit_acute_senses'] == 'yes')
  $special_abilities[] = 'Acute senses';

if($this_pet['merit_berserker'] == 'yes')
  $special_abilities[] = 'Berserker';

if($this_pet['merit_ravenous'] == 'yes')
  $special_abilities[] = 'Bottomless stomach';

if($this_pet['merit_catlike_balance'] == 'yes')
  $special_abilities[] = 'Cat-like balance';

if($this_pet['merit_moonkin'] == 'yes')
  $special_abilities[] = 'Child of the Moon';

if($this_pet['merit_careful_with_equipment'] == 'yes')
  $special_abilities[] = 'Handy';

if($this_pet['merit_light_sleeper'] == 'yes')
  $special_abilities[] = 'Light sleeper';

if($this_pet['merit_lightning_calculator'] == 'yes')
  $special_abilities[] = 'Lightning calculator';

if($this_pet['merit_lucky'] == 'yes')
  $special_abilities[] = 'Luck of the fae';

if($this_pet['merit_medium'] == 'yes')
  $special_abilities[] = 'Medium';

if($this_pet['merit_predicts_earthquakes'] == 'yes')
  $special_abilities[] = 'Predicts earthquakes';

if($this_pet['merit_pruriency'] == 'yes')
  $special_abilities[] = 'Pruriency';

if($this_pet['merit_silver_tongue'] == 'yes')
  $special_abilities[] = 'Silver tongue';

if($this_pet['merit_sleep_walker'] == 'yes')
  $special_abilities[] = 'Sleep-walker';

if($this_pet['merit_transparent'] == 'yes')
  $special_abilities[] = 'Star student';

if($this_pet['merit_steady_hands'] == 'yes')
  $special_abilities[] = 'Steady hands';

if($this_pet['merit_tough_hide'] == 'yes')
  $special_abilities[] = 'Tough hide';

foreach($KNACKS as $knack=>$description)
{
  $stat = $this_pet[$knack];

  if($stat == 1)
    $special_abilities[] = 'Knack for ' . $description;
  else if($stat == 2)
    $special_abilities[] = 'Talent for ' . $description;
  else if($stat == 3)
    $special_abilities[] = 'Gift for ' . $description;
}

$pdf = new BetterFPDF('Portrait', 'pt', 'Letter');

$pdf->SetMargins(0, 0, 0);
$pdf->SetTextColor(0, 0, 0);

$pdf->SetFont('Helvetica', 'B', 20);
$pdf->Text(0, 0, $this_pet['petname']);

$pdf->SetFont('Helvetica', '', 12);
$pdf->Text(0, 20, $WESTERN_ZODIAC[get_western_zodiac($this_pet['birthday'])] . ' ' . $CHINESE_ZODIAC_EN[get_chinese_zodiac($this_pet['birthday'])]);

//$pdf->Image('http://' . $SETTINGS['static_domain'] . '/gfx/pets/' . $this_pet['graphic'], $pdf->GetX(), $Top, 2, 2);
/*
$pdf->SetXY(2 * 72, 32 * 72);
$pdf->Cell(0.5, 0.5, $Body, 0, 1);
$pdf->Cell(0.5, 0.5, $Mind, 0, 1);
$pdf->Cell(0.5, 0.5, $Special, 0, 1);
*/
$pdf->Output();
