$(function() {
  $('#save_mininote').submit(function() {
    $('#save_mininote input').attr('disabled', 'disabled');
    $('#save_mininote .throbber').show();
    $.ajax({
      url: '/pet/savenote.php',
      type: 'POST',
      data: {
        petid: $('#save_mininote input[name="petid"]').val(),
        mininote: $('#mininote_note').val()
      },
      success: function()
      {
        $('#save_mininote input').removeAttr('disabled');
        $('#save_mininote .throbber').hide();
      }
    });
  
    return false;
  });
});
