function check_all_mail()
{
  i = document.maillist.elements.length;
  for(j = 1; j < i; ++j)
  {
    document.maillist.elements[j].checked = document.maillist.checkall.checked;
  }
}

function color_table_rows()
{
  $('#mailrows tr').removeClass('row');
  $('#mailrows tr').removeClass('altrow');
  $('#mailrows .mailrow:even').addClass('altrow');
  $('#mailrows .mailrow:odd').addClass('row');
  $('#mailrows .mailbodyrow:even').addClass('altrow');
  $('#mailrows .mailbodyrow:odd').addClass('row');
}

loadedmail = [];
readingmail = [];
actingon = [];

function movemail(id)
{
  actingon[id] = true;
  to = $('#whereto' + id).val();

  $.ajax({
    type: 'POST',
    url: 'ajax_movemail.php',
    data: 'id=' + id + '&to=' + to
  });

  $('#mailbodyxhtml' + id).slideUp(
    'fast',
    function()
    {
      $('#mailbody' + id).remove();
      $('#mail' + id).remove();

      color_table_rows();
    }
  );
}

function deletemail(id)
{
  actingon[id] = true;

  $.ajax({
    type: 'POST',
    url: 'ajax_deletemail.php',
    data: 'id=' + id
  });

  $('#mailbodyxhtml' + id).slideUp(
    'fast',
    function()
    {
      $('#mailbody' + id).remove();
      $('#mail' + id).remove();

      color_table_rows();
    }
  );
}

function readmail(id)
{
  if(actingon[id])
    ;
  else if(readingmail[id])
  {
    actingon[id] = true;

    $('#mailbodyxhtml' + id).slideUp(
      'fast',
      function()
      {
        $('#mailbody' + id).hide();
        actingon[id] = false;
        readingmail[id] = false;
      }
    );
  }
  else if(loadedmail[id])
  {
    actingon[id] = true;

    $('#mailbody' + id).show();
    $('#mailbodyxhtml' + id).slideDown(
      'fast',
      function()
      {
        actingon[id] = false;
        readingmail[id] = true;
      }
    );
  }
  else
  {
    actingon[id] = true;

    $('#mailbody' + id).show();

    $.ajax({
      type: 'POST',
      url: 'ajax_readmail.php',
      data: 'id=' + id,
      success:
        function(msg)
        {
          $('#mailbodyxhtml' + id).slideUp(
            'fast',
            function()
            {
              $('#mailbodyxhtml' + id).html(msg);
              $('#mailbodyxhtml' + id).slideDown('fast');

              if($('#mail' + id).is('.newmail'))
              {
                $('#mail' + id).removeClass('newmail');
                $('#mailicon' + id).html('<img src="gfx/mail_read.png" width="16" height="16" alt="(read)" />');
              }

              actingon[id] = false;
              loadedmail[id] = true;
              readingmail[id] = true;
            }
          );
        }
    });
  }
}

function star_mail(mailid)
{
  $('#starmail' + mailid).html('<img src="gfx/throbber.gif" />');

  $.ajax({
    type: 'POST',
    url: 'ajax_starmail.php',
    data: 'id=' + mailid,
    success: function(data)
    {
      if(data == 'OK')
        $('#starmail' + mailid).html(
          '<a href="#" onclick="unstar_mail(' + mailid + '); return false;" style="color:#c93; font-weight: normal;">&#9733;</a>'
        );
      else
        $('#starmail' + mailid).html(
          '<span class="failure">?</span>'
        );
    }
  });
}

function unstar_mail(mailid)
{
  $('#starmail' + mailid).html('<img src="gfx/throbber.gif" />');

  $.ajax({
    type: 'POST',
    url: 'ajax_unstarmail.php',
    data: 'id=' + mailid,
    success: function(data)
    {
      if(data == 'OK')
        $('#starmail' + mailid).html(
          '<a href="#" onclick="star_mail(' + mailid + '); return false;" class="dim" style="font-weight: normal;">&#9734;</a>'
        );
      else
        $('#starmail' + mailid).html(
          '<span class="failure">?</span>'
        );
    }
  });
}

$(function() {
  $('#mailtable').tablesorter({
    headers: {
      0: { sorter: false },
      1: { sorter: false },
      3: { sorter: false },
    }
  });
  
  $('#mailtable').bind('sortEnd', color_table_rows);
});