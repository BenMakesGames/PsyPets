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
  document.getElementById('kitchen').style.top = f_scrollTop() + 120 + 'px';
  document.getElementById('kitchen').style.left = ((document.body.offsetWidth - document.getElementById('kitchen').style.width) / 2) + 'px';
  document.getElementById('kitchen').style.display = '';

  var string = firework_string.replace(new RegExp('%postid%', 'g'), postid);
  string = string.replace('%resident%', resident)

  document.getElementById('kitchen').innerHTML = string;
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
