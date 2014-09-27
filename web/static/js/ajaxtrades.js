var loaded = [];

function toggle_group(itemid)
{
  $('.group_' + itemid).attr('checked', $('input[name=g_' + itemid + ']').attr('checked'));
}

function show_group(itemid)
{
  if(loaded[itemid] == true)
  {
    $('#group_row_' + itemid).show();
  }
  else
  {
    $('#group_' + itemid).html('<center><img src="gfx/throbber.gif" /></center>');
    $('#action_' + itemid).html('&#x25BC;');
    $('#group_row_' + itemid).show();

    $.ajax({
      type: 'POST',
      url: 'ajax_get_inventory_group.php',
      cache: false,
      data: 'location=storage&itemid=' + itemid + '&checked=' + $('input[name=g_' + itemid + ']').attr('checked'),
      success: function(data)
      {
        var newline = data.indexOf("\n");
        var itemid = data.substr(0, newline);
        var xhtml = data.substr(newline + 1);

        $('#group_' + itemid).html(xhtml);
      },
      error: function(bla, msg, bla2)
      {
        alert('Error: ' + msg);
      }
    });
    
    loaded[itemid] = true;
  }
}

function hide_group(itemid)
{
  $('#group_row_' + itemid).hide();
}

function show_items_to_trade()
{
  $('#show_items_button').attr('disabled', true);
  $('#items_to_trade').html('<h5>Items to Trade</h5><p><img src="gfx/throbber.gif" alt="loading..." /></p>');

  $.ajax({
    'type': 'POST',
    'url': 'ajax_get_inventory_to_trade.php',
    success: function(data)
    {
      $('#items_to_trade').html(data);
    }
  });
}
