var num_confetti = 20;
var confetti_colors = ['red','green','red','orange','purple','pink','lightGreen','lightBlue'];
var confetti_symbols = ['2605', '2730', '272F', '272B', '272C', '2727', '2729','263B'];
var sinkspeed = 1;
var max_size = 25;
var min_size = 10;

var confetti = [];
var i_confetti = 0;
var content_top;
var content_left;
var content_height;
var content_width;
var size_range = max_size - min_size + 1;

function rnd(range) { return Math.floor(range * Math.random()); }

function animate_confetti()
{
  for(i = 0; i <= num_confetti; i++)
  {
    confetti[i].posy += confetti[i].sink;
    confetti[i].style.left = confetti[i].posx + Math.sin(confetti[i].posy / 10 + confetti[i].sin_offset) + 'px';
    confetti[i].style.top = confetti[i].posy + 'px';

    if(confetti[i].style.opacity <= 0.02)
    {
      confetti[i].style.fontSize = confetti[i].size + 'px';
      confetti[i].style.color = confetti_colors[rnd(confetti_colors.length)];
      confetti[i].style.opacity = 1;
    }
    else
      confetti[i].style.opacity -= 0.02;
     
    if(confetti[i].style.opacity <= 0.02)
    {
      confetti[i].posx = content_left + rnd(content_width - confetti[i].size);
      confetti[i].posy = content_top + 32;
    }
  }

  setTimeout('animate_confetti()', 50);
}

$(function() {
  var content = $('#content');
  var p = content.offset();

  content_height = content.outerHeight();
  content_width = content.outerWidth();
  content_top = p.top;
  content_left = p.left;

  for(i = 0; i <= num_confetti; i++)
    content.append('<div id="confetti_' + i + '" style="position:absolute;top:-' + max_size + 'px;">&#x' + confetti_symbols[rnd(confetti_symbols.length)] + ';</div>')

	for(i = 0; i <= num_confetti; i++)
  {
    confetti[i] = document.getElementById('confetti_' + i);
    confetti[i].sin_offset = rnd(314);
    confetti[i].size = rnd(size_range) + min_size;
    confetti[i].style.fontSize = confetti[i].size + 'px';
    confetti[i].style.color = confetti_colors[rnd(confetti_colors.length)];
    confetti[i].sink = sinkspeed * confetti[i].size / 5;
    confetti[i].posx = content_left + rnd(content_width - confetti[i].size);
    confetti[i].posy = content_top + 32;
    confetti[i].style.left = confetti[i].posx + 'px';
    confetti[i].style.top = confetti[i].posy + 'px';
    confetti[i].style.opacity = Math.random();
  }

  animate_confetti();
});
