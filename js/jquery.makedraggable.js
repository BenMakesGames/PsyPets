/**
* Makes matched element draggable
* @param {number} delay (in miliseconds, default 200)
* @returns {jQuery}
*/
$.fn.makeDraggable = function(delay)
{
  if(typeof(delay) == 'undefined') delay = 200;

  var a = false, // drag active
  d = $(document), // document jQuery
  e = $(this), // current element
  p = [null,null], // clicked point
  m = null, // current mouseDown event
  t = null; // timeout id

  var bl = function(e)
  { // blocks an event (to prevent selecting text)
    try
    { // will fail with IE & timeout
      e.stopPropagation();
      e.preventDefault();
    }
    catch (e)
    {
    } // but will still remain usable
  };

  var md = function(e_md)
  { // mousedown
    bl(e_md); // text selection would occur if not blocked here
    if(a)
    { // drag active
      e.parent().css('opacity', '0.7')
        .css('outline', '3px dashed #fc0');
      d.bind('mousemove', mm);
      
      p = [e_md.clientX - e.parent().position().left, e_md.clientY - e.parent().position().top];
      e.trigger('dragstart');
    }
    else
    { // just wait
      m = e_md;
      t = setTimeout(to, delay);
    }
  };

  var mu = function(e_mu)
  { // mouseup
    if(a)
    { // drag active
      bl(e_mu);
      d.unbind('mousemove', mm);
        e.parent().css('opacity', '1')
        .css('outline', 'none')
        .trigger('dragend');
    }

    a = false;
    clearTimeout(t);
  };

  var mm = function(e_mm)
  { // mousemove
    bl(e_mm);
    e.parent().css('left', (e_mm.clientX - p[0]) + 'px')
      .css('top', (e_mm.clientY - p[1]) + 'px');
  };

  var to = function()
  { // timeout
    a = true;
    if (m) md(m);
  };

  e.bind('mousedown', md).bind('mouseup', mu);

  return e;
};
