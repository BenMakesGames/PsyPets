<?php
class User extends psyDBObject
{
  // read
  public function ID() { return $this->_data['idnum']; }
  public function Name() { return $this->_data['display']; }

  public function SiteLayout() { return $this->_data['style_layout']; }
  public function SiteColorTheme() { return $this->_data['style_color']; }
  public function SiteBackground() { return $this->_data['style_background']; }

  public function Groups() { return take_apart(',', $this->_data['groups']); }

  public function HasReadToS() { return($this->_data['readtos'] == 'yes'); }
  public function UseEmotes() { return $this->_data['emote']; }

  public function LocalTime($time = false)
  {
    if($time === false) $time = time();

    if($this->_data['daylightsavings'] == 'yes')
      $ds = 3600; // +1 hour
    else
      $ds = 0;

    //                             time    user time-zone    daylight savings
    return gmdate('Y.m.d, h:ia', $time + (60 * 60 * $this->_data['timezone']) + $ds);
  }

  public function IsPassword($password) { return(md5($password) == $this->_data['pass']); }

  public function IsDisabled() { return($this->_data['disabled'] != 'no'); }
  public function IsActivated() { return($this->_data['activated'] == 'yes'); }

  public function BonusMaxPets()
  {
    $bonus = 0;
  
    if($this->_data['license'] == 'yes')
      $bonus += 2;

    if($this->_data['breeder'] == 'yes')
      $bonus += 10;

    return $bonus;
  }
  
  // write
  public function ReadToS()
  {
    $this->_data['readtos'] = 'yes';

    $this->Update(array(
      'set' => array('readtos=\'yes\''),
      'where' => array('idnum=' . (int)$this->_data['idnum']),
      'limit' => 1
    ));
  }

  public function LogIn()
  {
    if($this->_data['multi_login'] == 'yes' && $this->_data['lastactivity'] >= $now - 15 * 60 && $this->_data['sessionid'] != 0)
      $sessionid = $this->_data['sessionid'];
    else
      $sessionid = mt_rand(1, 4000000);

    $now = time();
    $ip = get_ip();

    $this->Update(array(
      'set' => array(
        'sessionid=' . $sessionid,
        'logintime=' . $now,
        'parentalaccess=\'no\'',
        'last_ip_address=' . $this->QuoteString($ip),
        'logins=logins+1',
        'show_tip=tips_enabled',
      ),
      'where' => array('idnum=' . $this->_data['idnum']),
    ));

		require_once 'commons/statlib.php';
		record_stat($this->_data['idnum'], 'Logged In', 1);
		
    LoginHistory::CreateRecord($this->_data['idnum'], $ip);

    $this->_data['sessionid'] = $sessionid;
    $this->_data['logintime'] = $now;
    $this->_data['parentalaccess'] = 'no';
    $this->_data['last_ip_address'] = $ip;
    $this->_data['logins']++;
    $this->_data['show_tip'] = $this->_data['tips_enabled'];

    if($this->_data['logins'] == 1)
    {
      $house = House::GetByOwnerID($this->_data['idnum']);
      $house->FirstLogIn();

      header('Location: /myhouse.php');
      exit();
    }
  }

  // static helper functions
  public static function Link($display, $extra = '')
  {
    return '<a href="/residentprofile.php?resident=' . link_safe($display) . '">' . $display . $extra . '</a>';
  }

  // factories
  static public function GetByID($idnum)
  {
    $new_user = new User();

    $new_user->SelectOne(array(
      'where' => array('idnum=' . (int)$idnum),
    ));

    return $new_user;
  }

  static public function GetByName($name)
  {
    $new_user = new User();

    $new_user->SelectOne(array(
      'where' => array('display=' . $new_user->QuoteString($name)),
    ));

    return $new_user;
  }

  static public function GetByLogin($user)
  {
    $new_user = new User();

    $new_user->SelectOne(array(
      'where' => array('user=' . $new_user->QuoteString($user))
    ));

    return $new_user;
  }

  static public function GetBySession()
  {
    $new_user = new User();
		global $SETTINGS;

    if($_POST['login_name'] && $_POST['login_password'])
    {
      $new_user = User::GetByLogin($_POST['login_name']);

      if(FailedLogins::RecentFailCount($_POST['login_name']) >= 5)
      {
        $new_user = new User();
        add_cookie_message('<span class="failure">There have been an awful lot of failed login attempts to this account.  Please try again in a few minutes.</span>');
      }
      else if($new_user->IsPassword($_POST['login_password']) && $new_user->IsActivated() && !$new_user->IsDisabled())
      {
        $new_user->LogIn();

        setcookie($SETTINGS['cookie_name'], $new_user->_data['idnum'] . ';' . $new_user->_data['sessionid'], time() + $new_user->_data['login_persist'], $SETTINGS['cookie_path'], $SETTINGS['cookie_domain']);
    
        return $new_user;
      }
      else
			{
        FailedLogins::Log($_POST['login_name'], $new_user->ID() > 0);
      
        $new_user = new User();
				add_cookie_message('<span class="failure">Login name and password do not match.</span>');
			}
    }
		else
		{
			if($_POST['login_name'] || $_POST['login_password'])
				add_cookie_message('<span class="failure">Login name and password must both be provided.</span>');
		}
		
    list($userid, $sessionid) = take_apart(';', $_COOKIE['psypets_session']);
    $userid = (int)$userid;
    $sessionid = (int)$sessionid;

    if($userid > 0 && $sessionid > 0)
    {
      $new_user->SelectOne(array(
        'where' => array(
          'idnum=' . $userid,
          'sessionid=' . $sessionid,
          'disabled=\'no\'',
          'activated=\'yes\''
        )
      ));

      if($new_user->IsLoaded())
        setcookie('psypets_session', $new_user->_data['idnum'] . ';' . $new_user->_data['sessionid'], time() + $new_user->_data['login_persist'], '/', '.psypets.net');
    }

    return $new_user;
  }

  protected function __construct() { parent::__construct('monster_users'); }
}
