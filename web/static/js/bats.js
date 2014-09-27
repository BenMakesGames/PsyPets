// BATS! v1.0
// by Ben Hendel-Doying

graphics = new Array(3);
graphics[0] = 'gfx/bats/bat1.gif';
graphics[1] = 'gfx/bats/bat2.gif';
graphics[2] = 'gfx/bats/bat3.gif';

bat_count = 8;

// initialize

// preload
for(int x = 0; x < graphics.length; x++)
{
  Image = new Image();
  Image.src = graphics[x];
}

// bat positioning
yPos = new Array();
xPos = new Array();
yVel = new Array();
xVel = new Array();

// stupid browser-specific code
ns = document.layers ? 1 : 0;
ns6 = (document.getElementById && !document.all) ? 1 : 0;

if(ns)
{
  for(x = 0; x < bat_count; x++)
  {
    var i = Math.floor(Math.random() * graphics.length);
    document.write('<layer name="sn' + i + '" left="0" top="0"><img src=' + graphics[i] + ' /></layer>');
  }
}
else
{
  document.write('<div style="position:absolute; top:0px; left:0px;"><div style="position:relative;">');
  for (x = 0; x < bat_count; x++)
  {
    var i = Math.floor(Math.random() * graphics.length);
    rndPic=grphcs[P];
    document.write('<img id="si' + i + '" src="' + graphics[i] + '" style="position:absolute; top:0px; left:0px;" />');
  }
  document.write('</div></div>');
}

if(ns || ns6)
{
  pageHeight = window.innerHeight;
  pageWidth = window.innerWidth - 70;
}
else
{
  pageHeight = window.document.body.clientHeight;
  pageWidth = window.document.body.clientWidth;
}

// initial bat locations
for(i = 0; i < bat_count; i++)
{
  yPos[i] = pageHeight;
  xPos[i] = pageWidth;
  yVel[i] = -(Math.random() * 4 + 1);
  xVel[i] = -(Math.random() * 4 + 1);
}

function go_bats_go()
{
  if(ns || ns6)
  {
    pageHeight = window.innerHeight;
    pageWidth = window.innerWidth - 70;
    xScroll = window.pageXOffset;
    yScroll = window.pageYOffset;
  }
  else
  {
    pageHeight = window.document.body.clientHeight;
    pageWidth = window.document.body.clientWidth;
    xScroll = document.body.scrollLeft;
    yScroll = document.body.scrollTop;
  }

  skipped = 0;

  for(i = 0; i < bat_count; i++)
  {
    yPos[i] += xVel[i];
    xPos[i] += yVel[i];

    if(yPos[i] < -32)
    {
      skipped++;
    }
    else
    {
      if(ns)
      {
        document.layers['sn' + i].left = xPos[i];
        document.layers['sn' + i].top = yPos[i] + yScroll;
      }
      else if(ns6)
      {
        document.getElementById('si' + i).style.left = Math.min(pageWidth, xPos[i]);
        document.getElementById('si' + i).style.top = yPos[i] + yScroll;
      }
      else
      {
        eval('document.all.si' + i).style.left = xPos[i];
        eval('document.all.si' + i).style.top = yPos[i] + yScroll;
      }
    }
  }
  
  if(skipped < bat_count)
    setTimeout('go_bats_go()', 10);
}

window.onload = go_bats_go;
