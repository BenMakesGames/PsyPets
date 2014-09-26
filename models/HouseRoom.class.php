<?php
class HouseRoom extends psyDBObject
{
  public function ID() { return $this->_data['idnum']; }
  public function Name() { return $this->_data['name']; }

  public function IsVisible() { return($this->_data['visible'] == 'yes'); }
  public function IsLocked() { return($this->_data['protected'] == 'yes'); }
  
  public function RenderImageXHTML()
  {
    return '<img src="//saffron.psypets.net/gfx/rooms/' . $this->_data['appearance'] . '" width="' . $this->_data['pixel_width'] . '" height="' . $this->_data['pixel_height'] . '" alt="' . $this->_data['name'] . '" title="' . $this->_data['name'] . '" />';
  }

  public function PixelX()
  {
    return 28 + ($this->_data['x']) * 28 - ($this->_data['y'] % 2) * 14 - $this->_data['pixel_width'] / 2;
  }

  public function PixelY()
  {
    return 14 + $this->_data['y'] * 7 - $this->_data['pixel_height'];
  }

  protected function __construct() { parent::__construct('psypets_house_rooms'); }

  static public function GetById($id)
  {
    $house_room = new HouseRoom();
  
    $command = '
      SELECT * FROM psypets_house_rooms
      WHERE idnum=' . (int)$id . '
      LIMIT 1
    ';
    $house_room->_data = $house_room->FetchSingle($command);
    
    return $house_room;
  }

  static public function GetByOwnerId($id)
  {
    $house_room = new HouseRoom();
  
    $command = '
      SELECT * FROM psypets_house_rooms
      WHERE userid=' . (int)$id . '
    ';
    
    $room_data_set = $house_room->FetchMultiple($command);
    
    $rooms = array();
    
    foreach($room_data_set as $room_data)
    {
      $new_room = new HouseRoom();
      $new_room->_data = $room_data;
      
      $rooms[] = $new_room;
    }
    
    return $rooms;
  }
}
