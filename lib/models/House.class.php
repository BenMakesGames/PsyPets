<?php
class House extends psyDBObject
{
  public static $MAX_BULK = 100000;

  // read
  public function ID() { return $this->_data['idnum']; }
  public function OwnerID() { return $this->_data['userid']; }

  public function HasAddOn($addon) { return in_array($addon, $this->_addons); }

  public function MaxBulk() { return min(House::$MAX_BULK, $this->_data['maxbulk']); }
  public function RealMaxBulk() { return $this->_data['maxbulk']; }

  public function IsOverFull() { return($this->_data['curbulk'] > $this->MaxBulk()); }
  
  public function Wallpaper()
  {
    list($wallpaper) = explode(',', $this->_data['wallpapers']);
    return $wallpaper;
  }
  
  public function MaxPets()
  {
    $multiplier = 98;

    if(!$this->HasAddOn('Kitchen'))
      $multiplier -= 20;

    if(!$this->HasAddOn('Farm'))
      $multiplier -= 20;

    if(!$this->HasAddOn('Attic'))
      $multiplier -= 5;

    if(!$this->HasAddOn('Basement'))
      $multiplier -= 5;

    if(!$this->HasAddOn('Tower'))
      $multiplier -= 5;

    $max_pets = floor($this->MaxBulk() * $multiplier / House::$MAX_BULK) + 2;

    if(!$this->HasAddOn('Windmill'))
      $max_pets++;

    if(!$this->HasAddOn('Refreshing Spring'))
      $max_pets++;

    return $max_pets;
  }
  
  public function Hours()
  {
    return floor((time() - $this->_data['lasthour']) / (60 * 60));
  }
  
  public function RenderHouseBulkXHTML()
  {
    $effective_max_bulk = $this->MaxBulk();

    if($effective_max_bulk < $this->RealMaxBulk())
      $house_note = '<a href="realestate.php">*</a>';
    else
      $house_note = '';

    return ($this->_data['curbulk'] / 10) . '/' . ($effective_max_bulk / 10) . $house_note . '; ' . ceil($this->_data['curbulk'] * 100 / $effective_max_bulk) . '% full';
  }

  public function RenderRoomTabsXHTML($this_room = false, $this_addon = false)
  {
      global $SETTINGS;
    $rooms = $this->Rooms();
    $addons = $this->AddOns();

    $xhtml = '<ul class="tabbed">';

    foreach($rooms as $room)
    {
      if($room->IsVisible())
      {
        $classes = array();
        if($room->IsLocked())
          $classes[] = 'locked-room';
        if($room->Name() == $this_room)
          $classes[] = 'activetab';
      
        $xhtml .= ' <li class="' . implode(' ', $classes) . '"><nobr><a href="/myhouse.php?room=' . link_safe($room->ID()) . '">' . $room->Name() . '</a></nobr></li>';
      }
    }

    foreach($addons as $addon)
    {
      if($addon->IsVisible())
      {
        $classes = array('addontab');
        if($addon->Name() == $this_addon)
          $classes[] = 'activetab';
      
        $xhtml .= ' <li class="' . implode(' ', $classes) . '" style="background-image: url(//' . $SETTINGS['static_domain'] . '/gfx/addons/' . urlize($addon->Name()) . '.png);"><nobr><a href="/myhouse/addon/' . urlize($addon->Name()) . '.php">' . $addon->Name() . '</a></nobr></li>';
      }
    }

    $xhtml .= '<li style="border: 0; background-color: transparent;"><a href="/myhouse/managerooms.php"><img src="/gfx/pencil_small.png" height="13" width="15" alt="(manage rooms)" style="vertical-align:text-bottom;" /></a></li></ul>';
    
    return $xhtml;
  }

  public function Rooms()
  {
    static $rooms;

    if(!$rooms)
      $rooms = HouseRoom::GetByOwnerId($this->_data['userid']);

    return $rooms;
  }

  public function AddOns()
  {
    static $addons;

    if(!$addons)
      $addons = HouseAddOn::GetByOwnerId($this->_data['userid']);

    return $addons;
  }
  
  // write
  public function FirstLogIn()
  {
    $now = time();
  
    $this->Update(array(
      'set' => array('lasthour=' . $now),
      'where' => array('idnum=' . $this->_data['idnum']),
      'limit' => 1
    ));
    
    $this->_data['lasthour'] = $now;
  }

  // factories
  protected function __construct() { parent::__construct('monster_houses'); }

  private $_addons, $_rooms;

  private function PreProcess()
  {
    $this->_addons = take_apart(',', $this->_data['addons']);
    $this->_rooms = take_apart(',', $this->_data['rooms']);
  }

  static public function GetByID($idnum)
  {
    $new_house = new House();

    $new_house->_data = $new_house->SelectOne(array(
      'where' => array('idnum=' . (int)$idnum)
    ));

    $new_house->PreProcess();

    return $new_house;
  }

  static public function GetByOwnerID($userid)
  {
    $new_house = new House();

    $new_house->_data = $new_house->SelectOne(array(
      'where' => array('userid=' . (int)$userid)
    ));

    $new_house->PreProcess();

    return $new_house;
  }
}
?>
