// Toggle a range of checkboxes by clicking the first and shift-clicking the last.

var cur;

var checkEvent = function(e)
{
  if(e.button == 0 || e.keyCode == 32) // clicked, or pressed SPACE
  {
    // already have a starting point, and shift is pressed?
    if(cur && e.shiftKey) selectRange(cur, e.target);
    cur = e.target;
  }
}

var selectRange = function(from, to)
{
  var inputs = document.getElementsByTagName('input');
  var checkboxes = [];
  var last;
  var i;

  // this might be better with xpath, but I'm lazy
  for(i = 0; i < inputs.length; i++)
  {
    if(inputs[i].getAttribute('type') == 'checkbox' && inputs[i].getAttribute('title') != 'Check row')
      checkboxes.push(inputs[i]);
  }

  for(i = 0; i < checkboxes.length; i++)
  {
    if(checkboxes[i] == to)
    {
      last = from;
      break;
    }

    if(checkboxes[i] == from)
    {
      last = to;
      break;
    }
  }

  for(; i < checkboxes.length; i++)
  {
    if(checkboxes[i] != from && checkboxes[i] != to) // from and to have been clicked by the user
    {
      if(checkboxes[i].checked != from.checked) // state change?
        checkboxes[i].checked = !checkboxes[i].checked;
    }

    if(checkboxes[i] == last)
      break;
  }
}

var init_selectrange = function()
{
  if(document.documentElement.addEventListener)
  {
    $('input:checkbox').click(checkEvent);
    $('input:checkbox').keyup(checkEvent);
  }
  else if(document.documentElement.attachEvent) // IE
  {
    $('input:checkbox').click(checkEvent);
    $('input:checkbox').keypress(checkEvent);
  }
}

var reinit_selectrange = function(id)
{
  if(document.documentElement.addEventListener)
  {
    $('#' + id).click(checkEvent);
    $('#' + id).keyup(checkEvent);
  }
  else if(document.documentElement.attachEvent) // IE
  {
    $('#' + id).click(checkEvent);
    $('#' + id).keypress(checkEvent);
  }
}

// ---

var previous_scrolltop;

function encyclopedia_popup(element, itemname)
{
  element = $(element);

  offset = element.offset();

  offset.top -= 300;

  if(offset.top <= 16 + $(window).scrollTop())
    offset.top = 16 + $(window).scrollTop();

  if(offset.left >= $(document).width() - 448)
    offset.left -= 432;
  else
    offset.left += 48;

  $('#encyclopedia_entry_title').html(decodeURIComponent((itemname+'').replace(/\+/g, '%20')));

  $('#encyclopedia_entry').html('<center><img src="/gfx/throbber.gif" /></center>');

  if(!$('#encyclopedia_entry').is(':visible'))
  {
    previous_scrolltop = $(window).scrollTop();

    $('#encyclopedia_entry_box:hidden').css({top: offset.top + 'px', left: offset.left + 'px', marginTop: 0});
    $('#encyclopedia_entry_box:hidden').fadeIn();
  }

  $.ajax({
    cache: false,
    url: '/ajax_encyclopedia2.php',
    data: 'item=' + itemname,
    success:
      function(data)
      {
        $('#encyclopedia_entry').html(data);
      }
  });
}

// ---

jQuery.fn.preventDoubleSubmit = function()
{
  jQuery(this).submit(
    function()
    {
      if(this.beenSubmitted)
        return false;
      else
        this.beenSubmitted = true;
    }
  );
};

// Adds an animated flash effect
// minorly adapted from jQuery UI
jQuery.fn.animateFlash = function(o) {
  return this.queue(function() {
    var elem = $(this),
      times = 5,
      duration = 250,
      animateTo = 0;

    for (var i = 0; i < times; i++) {
      elem.animate({ opacity: animateTo }, duration, 'linear');
      animateTo = (animateTo + 1) % 2;
    }

    elem.animate({ opacity: animateTo }, duration, 'linear', function() {
      if (animateTo == 0) {
        elem.hide();
      }
    });

    elem
      .queue('fx', function() { elem.dequeue(); })
      .dequeue();
  });
};

$(function() {
  init_selectrange();

  $('form').preventDoubleSubmit();

  $('.draggybit').makeDraggable();

  previous_scrolltop = $(window).scrollTop();

  $(window).scroll(function(e) {
    $('#encyclopedia_entry_box:visible').css({marginTop: ($(window).scrollTop() - previous_scrolltop) + 'px'});
  });

  var head = document.getElementsByTagName('head')[0];
  
  $('input[type=checkbox].checkall').each(function() {
    $(this).show();
    $(this).click(function() {
      var this_id = $(this).attr('id');
      var form_id = this_id.replace(/check/, 'form');
      $('#' + form_id + ' input[type=checkbox]').attr('checked', $(this).attr('checked'));
    });
  });
  
  $('ul li div.flash-message').click(function() {
    $(this).parent().remove();
  });

    init_js_tabs();
});

function setInputSelection(el, pos)
{
	if(el.setSelectionRange)
		el.setSelectionRange(pos, pos);
	else if(el.createTextRange)
	{
		var range = el.createTextRange();
		range.collapse(true);
		range.moveEnd('character', pos);
		range.moveStart('character', pos);
		range.select();
	}
}

// from http://stackoverflow.com/questions/263743/how-to-get-caret-position-in-textarea
function getInputSelection(el) {
    var start = 0, end = 0, normalizedValue, range,
        textInputRange, len, endRange;

    if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
        start = el.selectionStart;
        end = el.selectionEnd;
    } else {
        range = document.selection.createRange();

        if (range && range.parentElement() == el) {
            len = el.value.length;
            normalizedValue = el.value.replace(/\r\n/g, "\n");

            // Create a working TextRange that lives only in the input
            textInputRange = el.createTextRange();
            textInputRange.moveToBookmark(range.getBookmark());

            // Check if the start and end of the selection are at the very end
            // of the input, since moveStart/moveEnd doesn't return what we want
            // in those cases
            endRange = el.createTextRange();
            endRange.collapse(false);

            if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
                start = end = len;
            } else {
                start = -textInputRange.moveStart("character", -len);
                start += normalizedValue.slice(0, start).split("\n").length - 1;

                if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
                    end = len;
                } else {
                    end = -textInputRange.moveEnd("character", -len);
                    end += normalizedValue.slice(0, end).split("\n").length - 1;
                }
            }
        }
    }

    return {
        start: start,
        end: end
    };
}

function editor_add(target, tag, attrs)
{
	target.focus();
	
	var selection = getInputSelection(target[0]);
	
	var text = target.val();
	
	var before, selected, after;
	
	var opening_tag, closing_tag;

	if(tag == 'img' || tag == 'hr')
	{
		opening_tag = '<' + tag + (attrs ? ' ' + attrs : '') + ' />';
		closing_tag = '';
	}
	else
	{
		opening_tag = '<' + tag + (attrs ? ' ' + attrs : '') + '>';
		closing_tag = '</' + tag + '>';
	}
	
	before = text.substr(0, selection.start);
	after = text.substr(selection.end);

	if(selection.start == selection.end)
	{
		selected = '';

		before = before + opening_tag;
		after = closing_tag + after;
	}
	else
	{
		selected = text.substr(selection.start, selection.end - selection.start);

		before = before + opening_tag + selected + closing_tag;
	}
		
	target.val(before + after);
	
	setInputSelection(target[0], before.length);
}

function init_textarea_editor()
{
	$('.textarea-editor').each(function() {
		var editor = $(this);
		var target = $('#' + editor.attr('data-target'));
		
		var strong = $('<li />');
		strong
			.html('<strong>strong</strong>')
			.click(function() { editor_add(target, 'strong'); })
			.appendTo(editor)
		;

		var em = $('<li />');
		em
			.html('<em>emphasis</em>')
			.click(function() { editor_add(target, 'em'); })
			.appendTo(editor)
		;

		var u = $('<li />');
		u
			.html('<u>underlined</u>')
			.click(function() { editor_add(target, 'u'); })
			.appendTo(editor)
		;

		var color = $('<li />');
		color
			.html('<span style="color:orange;">c</span><span style="color:red;">o</span><span style="color:purple;">l</span><span style="color:blue;">o</span><span style="color:green;">r</span>')
			.click(function() { editor_add(target, 'span', 'style="color:COLOR;"'); })
			.appendTo(editor)
		;
		
		var link = $('<li />');
		link
			.html('<a>link</a>')
			.click(function() { editor_add(target, 'a', 'href="URL"'); })
			.appendTo(editor)
		;

		var img = $('<li />');
		img
			.html('image')
			.click(function() { editor_add(target, 'img', 'src="URL"'); })
			.appendTo(editor)
		;
	});
}

function init_js_tabs()
{
    $('.js-tab-bar > li > a').on('click.js-tabs', function(e) {
        e.preventDefault();

        var oldTabSelector = $('.js-tab-bar > li.activetab > a').attr('href');
        var newTabSelector = $(this).attr('href');

        $(oldTabSelector).addClass('hidden');
        $('.js-tab-bar > li.activetab').removeClass('activetab');

        $(newTabSelector).removeClass('hidden');
        $(this).parent().addClass('activetab');
    });
}
