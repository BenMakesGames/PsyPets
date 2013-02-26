function ratead(id, vote)
{
  document.getElementById('ratead').innerHTML = '<center><img src="gfx/throbber.gif" alt="loading..." width="16" height="16" /></center>';

  $.ajax({
    type: 'POST',
    url: 'ratead.php',
    data: 'ad=' + id + '&option=' + vote + '&ajax=yes',
    success: function(data) { votedone(data); }
  });
  
  return false;
}

function votedone()
{
  $('#ratead').html('<p>Your vote has been recorded.  Thanks!</p>');
}
