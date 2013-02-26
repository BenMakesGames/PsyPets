function reveal_post(id)
{
  document.getElementById('p' + id).style.display = '';
  document.getElementById('p' + id + '-2').style.display = '';

  if(document.getElementById('p' + id + '-3'))
    document.getElementById('p' + id + '-3').style.display = '';

  document.getElementById('p' + id + '-4').style.display = '';
  document.getElementById('s' + id).style.display = 'none';
}

function firework_popup(postid, resident)
{
  var string = firework_string.replace(new RegExp('%postid%', 'g'), postid);
  string = string.replace('%resident%', resident)

  $('#kitchen').html(string);

  document.getElementById('kitchen').style.top = f_scrollTop() + 120 + 'px';
  document.getElementById('kitchen').style.left = ((document.body.offsetWidth - $('#kitchen').width()) / 2) + 'px';
  document.getElementById('kitchen').style.display = '';
}

function firework_hide()
{
  document.getElementById('kitchen').style.display = 'none';
}

function hoveron(id)
{
  $('#' + id).removeClass('transparent_image');
}

function hoveroff(id)
{
  $('#' + id).addClass('transparent_image');
}

function thumbsup(postid)
{
  $('#postvote' + postid).html('<img src="gfx/throbber_dotdotdot.gif" width="22" height="16" />');

  $.ajax({
    type: 'GET',
    url: 'ajax_vote_on_post.php',
    data: 'postid=' + postid + '&vote=1',
    success: vote_response
  });
}

function thumbsdown(postid)
{
  $('#postvote' + postid).html('<img src="gfx/throbber_dotdotdot.gif" width="22" height="16" />');

  $.ajax({
    type: 'GET',
    url: 'ajax_vote_on_post.php',
    data: 'postid=' + postid + '&vote=-1',
    success: vote_response
  });
}

function vote_response(msg)
{
  var response = msg.split("\n", 2);

  $('#postvote' + response[0]).html(response[1]);
}


function report_post(postid, resident)
{
  var report_string =
    '<div><p>Report this post by <b>%resident%</b>?</p>' +
    '<p>If the post is intentionally inflammatory, is illegal, or otherwise violates PsyPets\' <a href="termsofservice.php">Terms of Service</a>, please report it!</p>' +
    '<p>If you\'d like to provide any details, use the space below to do so, otherwise, you may leave it blank.</p>' +
    '<form action="reportpost.php?id=%postid%" method="post">' +
    '<p><textarea name="comment" style="width:100%;" rows="4"></textarea></p>' +
    '<p class="nomargin"><input type="submit" value="Report" /> <button onclick="firework_hide(); return false;" style="width:75px;">Cancel</button></p>' +
    '</form></div>'
  ;

  var string = report_string.replace(new RegExp('%postid%', 'g'), postid);
  string = string.replace('%resident%', resident)

  $('#kitchen').html(string);

  document.getElementById('kitchen').style.top = f_scrollTop() + 120 + 'px';
  document.getElementById('kitchen').style.left = ((document.body.offsetWidth - $('#kitchen').width()) / 2) + 'px';
  document.getElementById('kitchen').style.display = '';
}
