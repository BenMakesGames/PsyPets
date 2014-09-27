// a JavaScript library used in PsyPets to handle various AJAX functions

function stop_recording()
{
  $('#recordingautosort').html('<span class="dim">&#9632;</span> <blink style="color:red;">recording moves</blink>');

  $.ajax({
    type: 'POST',
    url: 'ajax_stoprecordingmoves.php',
    success: function()
    {
      $('#recordingautosort').html('<a href="#" onclick="start_recording(); return false;" style="color:red;">&#9679;</a>');
    }
  });
}

function start_recording()
{
  $('#recordingautosort').html('<span class="dim">&#9679;</span>');

  $.ajax({
    type: 'POST',
    url: 'ajax_startrecordingmoves.php',
    success: function()
    {
      $('#recordingautosort').html('<a href="#" onclick="stop_recording(); return false;">&#9632;</a> <blink style="color:red;">recording moves</blink>');
    }
  });
}

function move_items(action)
{
  if(action != 'move' && action != 'trash' && action != 'sell')
  {
    alert('did not understand action');
    return false;
  }

  var form = document.getElementById('homeaction').elements;
  var l = form.length;
  var j = 0;

  var ids = new Array();

  // determine which items are checked, and store their names (item ids) in a list
  for(var i = 0; i < l; ++i)
  {
    var element = form[i];

    if(element.type == 'checkbox' && element.checked)
      ids[j++] = element.name;
  }

  if(ids.length > 0)
  {
    // disable item checkboxes while we process them...
    for(i in ids)
    {
      var id = ids[i];
      $('#checkbox_' + id).html('<img src="gfx/throbber.gif" height="16" width="16" />');
    }

    var id_string = ids.join();
    var move_to = document.getElementById('move1').value;

//    alert('sending these item ids - ' + id_string + ' - to ' + move_to);

    $.ajax({
      type: 'POST',
      url: 'ajax_' + action + 'inventory2.php',
      data: 'ids=' + id_string + '&to=' + move_to,
      success: function(data) { update_inventory(data); }
    });
  }

  return false;
}

function update_inventory(data)
{
  var results = data.split("\n");

  for(i in results)
  {
    var command = results[i].substr(0, 8);
    var value = results[i].substr(8);
    
    if(command == 'message:')
    {
      $('#message_area').append('<p>' + value + '</p>');
    }
    else if(command == 'success:')
    {
      ids = value.split(",");
      
      for(i in ids)
      {
        var node = document.getElementById('item_' + ids[i]);
        node.parentNode.removeChild(node);
      }
    }
    else if(command == 'failure:')
    {
      ids = value.split(",");
      
      for(i in ids)
      {
        var id = ids[i];
        $('#checkbox_' + id).html('<input type="checkbox" id="' + id + '" name="' + id + '" />');
        reinit_selectrange('checkbox_' + id);
      }
    }
    else if(command == 'domoney:')
    {
      $('#moneysonhand').html(value);
    }
    else if(command == 'newbulk:')
    {
      $('#housebulk').html(value);
    }
    else
    {
      alert('Error: ' + data);
      break;
    }
  }
}

function trash_items()
{
  if(confirm("Really throw out these items?\nYou could give them away in the Giving Tree instead."))
    return move_items('trash');
  else
    return false;
}

function sell_items(ltc)
{
  var message = "Really sell these items?";

  if(ltc == 'yes')
    message += "\nYou might be able to make more money using the Flea Market.";

  if(confirm(message))
    return move_items('sell');
  else
    return false;
}

var recipes_displayed = false;

function check_uncheck(min, max, offset)
{
  for(i = min; i <= max; ++i)
  {
    document.homeaction.elements[i + 5 + offset].checked = document.homeaction.elements[min + 4 + offset].checked;
  }
}

function move_on(petid)
{
  if(confirm('Moving on is permanent! (Strangely, in PsyPets, death alone is not.)\nAre you sure you want to move on?'))
    window.location.href = "moveon.php?petid=" + petid;
}

function set_all_actions(actionindex)
{
  count = document.petactions.elements.length;
  for(i = 0; i < count; ++i)
  {
    if(document.petactions.elements[i].options && document.petactions.elements[i].options[0].value != 0)
      document.petactions.elements[i].selectedIndex = actionindex;
  }
}

function updatepreparewindow(data)
{
  $('#kitchen').html(data);
}

function openpreparewindow()
{
  document.getElementById('kitchen').style.top = f_scrollTop() + 120 + 'px';
  document.getElementById('kitchen').style.left = ((document.body.offsetWidth - 300) / 2) + 'px';
  $('#kitchen').fadeIn();

  if(recipes_displayed == false)
  {
    $.ajax({
      type: 'POST',
      url: 'ajax_recipes.php',
      success: function(data) { updatepreparewindow(data); }
    });

    did_house_menu = true;
  }

  return false;
}

function closepreparewindow()
{
  $('#kitchen').fadeOut();
}

function context_prepare()
{
  $('#prepare1').click();
}

function context_throwout()
{
  $('#throwout1').click();
}

function context_gamesell()
{
  $('#gamesell1').click();
}

function context_move(destination)
{
  $('#move1').val(destination).attr('selected', 'selected');
  $('#move2').val(destination).attr('selected', 'selected');
  $('#moveto1').click();
}

function context_feed(target)
{
  $('#pet1').val(target).attr('selected', 'selected');
  $('#pet2').val(target).attr('selected', 'selected');
  $('#feedto1').click();
}
