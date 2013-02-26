function add_remove_currency_for_resident(element, fieldid, residentname, currencyname, currencyid, currentamount)
{
  hide_currency_panel();

  element = $(element);

  offset = element.offset();

  if(offset.left >= $(document).width() - 210)
    offset.left = $(document).width() - 210;

  $('#currency_panel').css({top: offset.top + 'px', left: offset.left + 'px', marginTop: 0});

  $('#currency_panel').html(
    '<p><b>' + residentname + '\'s</b></p>' +
    '<p>' + currentamount + ' ' + currencyname + '</p>' +
    '<form onsubmit="update_currency(' + fieldid + '); return false;">' +
    '<input type="hidden" id="currency_currencyid" name="currencyid" value="' + currencyid + '" />' +
    '<input type="hidden" id="currency_resident" name="resident" value="' + residentname + '" />' +
    '<table><tr><td>' +
    '<input type="radio" name="currency_action" value="give" /> Give<br />' +
    '<input type="radio" name="currency_action" value="take" /> Take<br />' +
    '</td><td>' +
    '<input type="text" name="amount" id="currency_amount" />' +
    '</td></tr></table>' +
    '<input type="reset" value="Cancel" onclick="hide_currency_panel(); return false;" /> <input type="submit" value="OK" />' +
    '</form>'
  );

  $('#currency_panel').fadeIn();
}

function hide_currency_panel()
{
  $('#currency_panel').fadeOut(400, function() {
    $('input:radio[name=currency_action]:checked').removeAttr('checked');
    $('#currency_amount').val('');
  });
}

function update_currency(fieldid)
{
  resident_name = $('#currency_resident').val();
  currency_id = parseInt($('#currency_currencyid').val());
  give_or_take = $('input:radio[name=currency_action]:checked').val();
  amount = parseInt($('#currency_amount').val());
  
  all_is_well = true
  
  if(isNaN(currency_id))
  {
    all_is_well = false;
  }

  if(isNaN(amount))
  {
    all_is_well = false;
  }
  else if(amount < 0)
  {
    all_is_well = false;
  }

  if(give_or_take != 'give' && give_or_take != 'take')
  {
    all_is_well = false;
  }

  if(all_is_well)
  {
    // submit
    $('#field_' + fieldid).html('<img src="gfx/throbber.gif" />');

    hide_currency_panel();
    
    $.ajax({
      type: 'POST',
      url: 'ajax_manage_custom_currency.php',
      data: 'resident=' + resident_name + '&currency=' + currency_id + '&action=' + give_or_take + '&amount=' + amount + '&fieldid=' + fieldid,
      success: function(data) {
        $('#field_' + fieldid).html(data);
      }
    });
  }
}
