/*
  jQuery MegaMenu Plugin
  Author: GeekTantra
  Author URI: http://www.geektantra.com

  with (hackish) modifications by Ben Hendel-Doying
*/
var MM_IS_OPEN = false;
var MM_OBJECT;
var MM_FLOATING = false;
var MM_NORMAL_TOP;
var MM_NORMAL_HEIGHT;
var MM_FIRMLY_ATTACHED = false;

function mm_position()
{
  if(typeof(MM_OBJECT) == 'undefined')
    return;

  var mm_item_content_obj = MM_OBJECT.find('div.mm-item-content:visible');

  if(mm_item_content_obj.length == 0)
    return;

  var $mm_item_link = mm_item_content_obj.parent().find('.mm-item-link');

  if(MM_FLOATING)
  {
    mm_item_content_obj.css({
      'top': MM_NORMAL_HEIGHT + 'px',
      'left': $mm_item_link.offset().left - $('#me').offset().left - 5 + 'px'
    });
  }
  else
  {
    mm_item_content_obj.css({
      'top': ($mm_item_link.offset().top + $mm_item_link.outerHeight()) - 1 + 'px',
      'left': ($mm_item_link.offset().left) - 5 + 'px'
    });
  }
}

jQuery.fn.megamenu = function(options) {
  options = jQuery.extend({
                              activate_action: 'hover'
                          }, options);
  var $megamenu_object = this;
  var inner_click = false;

  jQuery(document).bind('click', function(e){
    if(inner_click)
    {
      inner_click = false;
      return true;
    }
    // only respond to left-click
    if(e.which == 1 || e.which == 0)
    {
      jQuery('a.mm-item-link').removeClass('mm-item-link-hover');
      jQuery('div.mm-item-content').hide();
    }
  });

  if( options.activate_action == "click" ) options.mm_timeout = 0;
  $megamenu_object.children("li").each(function(){
    jQuery(this).addClass("mm-item");
    jQuery(".mm-item").css({ 'float': 'left' });
    
    jQuery(this).find("div:first").addClass("mm-item-content");
    jQuery(this).find("a:first").addClass("mm-item-link");
    var $mm_item_content = jQuery(this).find(".mm-item-content");
    var $mm_item_link = jQuery(this).find(".mm-item-link");
    $mm_item_content.hide();

    jQuery(this).bind('click', function(e){
      inner_click = true;
    });
    $mm_item_content.wrapInner('<div class="mm-content-base"></div>');
    
    if(options.activate_action == 'hover')
    {
      var hoverintentconfig = {
        // Activation Method
        over: function(e) {
          jQuery('a.mm-item-link').removeClass('mm-item-link-hover');
          jQuery('div.mm-item-content').hide();

          var mm_item_link_obj = jQuery(this).find('a.mm-item-link');
          var mm_item_content_obj = jQuery(this).find('div.mm-item-content');

          mm_item_link_obj.addClass('mm-item-link-hover');

          if(MM_FLOATING)
          {
            mm_item_content_obj.css({
              'top': MM_NORMAL_HEIGHT + 'px',
              'left': $mm_item_link.offset().left - $('#me').offset().left - 5 + 'px'
            });
          }
          else
          {
            mm_item_content_obj.css({
              'top': ($mm_item_link.offset().top + $mm_item_link.outerHeight()) - 1 + 'px',
              'left': ($mm_item_link.offset().left) - 5 + 'px'
            });
          }

          var mm_object_right_end = $megamenu_object.offset().left + $megamenu_object.outerWidth();
                                    // Coordinates of the right end of the megamenu object
          var mm_content_right_end = $mm_item_link.offset().left + $mm_item_content.outerWidth() - 5 ;
                                    // Coordinates of the right end of the megamenu content
          if( mm_content_right_end >= mm_object_right_end ) { // Menu content exceeding the outer box
            mm_item_content_obj.css({
              'left': ($mm_item_link.offset().left - (mm_content_right_end - mm_object_right_end)) - 2 + 'px'
            }); // Limit megamenu inside the outer box
          }

          mm_item_content_obj.height("auto");
          mm_item_content_obj.fadeIn('fast');
          
          MM_IS_OPEN = true;
        },

        timeout: 350,

        // Deactivation Method
        out: function(e) {
          var mm_item_link_obj = jQuery(this).find('a.mm-item-link');
          var mm_item_content_obj = jQuery(this).find('div.mm-item-content');
          //mm_item_content_obj.stop();
          mm_item_link_obj.removeClass('mm-item-link-hover');
          mm_item_content_obj.fadeOut('fast');
          if(mm_item_content_obj.length < 1) mm_item_link_obj.removeClass("mm-item-link-hover");

          MM_IS_OPEN = false;
        }
      };
      
      jQuery(this).hoverIntent(hoverintentconfig);
/*
      jQuery(this).focus(function(e) {
          jQuery('a.mm-item-link').removeClass('mm-item-link-hover');
          jQuery('div.mm-item-content').hide();

          var mm_item_link_obj = jQuery(this).find('a.mm-item-link');
          var mm_item_content_obj = jQuery(this).find('div.mm-item-content');

          mm_item_link_obj.addClass('mm-item-link-hover');
          mm_item_content_obj.css({
            'top': ($mm_item_link.offset().top + $mm_item_link.outerHeight()) - 1 + 'px',
            'left': ($mm_item_link.offset().left) - 5 + 'px'
          });

          var mm_object_right_end = $megamenu_object.offset().left + $megamenu_object.outerWidth();
                                    // Coordinates of the right end of the megamenu object
          var mm_content_right_end = $mm_item_link.offset().left + $mm_item_content.outerWidth() - 5 ;
                                    // Coordinates of the right end of the megamenu content
          if( mm_content_right_end >= mm_object_right_end ) { // Menu content exceeding the outer box
            mm_item_content_obj.css({
              'left': ($mm_item_link.offset().left - (mm_content_right_end - mm_object_right_end)) - 2 + 'px'
            }); // Limit megamenu inside the outer box
          }
        });
      jQuery(this).blur(function(e) {
          var mm_item_link_obj = jQuery(this).find('a.mm-item-link');
          var mm_item_content_obj = jQuery(this).find('div.mm-item-content');
          //mm_item_content_obj.stop();
          mm_item_link_obj.removeClass('mm-item-link-hover');
          mm_item_content_obj.fadeOut('fast');
          if(mm_item_content_obj.length < 1) mm_item_link_obj.removeClass("mm-item-link-hover");
        });
*/
      // Deactivation Method Ends
    }
    else if(options.activate_action == 'click')
    {
      // Activation Method Starts
      jQuery(this).bind('click', function(e){
        inner_click = true;
        
        if($(this).find('.mm-item-content').is(':hidden'))
        {
          jQuery('.mm-item-content').hide();
          jQuery('.mm-item-link').removeClass("mm-item-link-hover");

          var mm_item_link_obj = jQuery(this).find("a.mm-item-link");
          var mm_item_content_obj = jQuery(this).find("div.mm-item-content");

          mm_item_link_obj.addClass("mm-item-link-hover");

          if(MM_FLOATING)
          {
            mm_item_content_obj.css({
              'top': MM_NORMAL_HEIGHT + 'px',
              'left': $mm_item_link.offset().left - $('#me').offset().left - 5 + 'px'
            });
          }
          else
          {
            mm_item_content_obj.css({
              'top': ($mm_item_link.offset().top + $mm_item_link.outerHeight()) - 1 + 'px',
              'left': ($mm_item_link.offset().left) - 5 + 'px'
            });
          }

          var mm_object_right_end = $megamenu_object.offset().left + $megamenu_object.outerWidth();
                                      // Coordinates of the right end of the megamenu object
          var mm_content_right_end = $mm_item_link.offset().left + $mm_item_content.outerWidth() - 5;
                                      // Coordinates of the right end of the megamenu content
          if( mm_content_right_end >= mm_object_right_end ) { // Menu content exceeding the outer box
            mm_item_content_obj.css({
              'left': ($mm_item_link.offset().left - (mm_content_right_end - mm_object_right_end)) - 2 + 'px'
            }); // Limit megamenu inside the outer box
          }

          mm_item_content_obj.height('auto');
          mm_item_content_obj.fadeIn('fast');

          MM_IS_OPEN = true;
        }
        else
        {
          var mm_item_link_obj = jQuery(this).find('a.mm-item-link');
          var mm_item_content_obj = jQuery(this).find('div.mm-item-content');
//          mm_item_content_obj.stop();
          mm_item_link_obj.removeClass('mm-item-link-hover');
          mm_item_content_obj.hide();
          if(mm_item_content_obj.length < 1) mm_item_link_obj.removeClass('mm-item-link-hover');

          MM_IS_OPEN = false;
        }
      });
    }
  });
  this.find(">li:last").after('<li class="clear-fix"></li>');
  this.show();
  
  return this;
};

$(function() {
  MM_NORMAL_TOP = $('#loggedin').offset().top;
  MM_NORMAL_HEIGHT = $('#loggedin').height() + 1;

  if(!MM_FIRMLY_ATTACHED)
  {
    $(window).scroll(function() {
      y = $(window).scrollTop();

      if(!MM_FLOATING && y >= MM_NORMAL_TOP)
      {
        var normal_width = $('#me').width();

        $('#me').css({'position': 'fixed', 'top': '0px', 'width': normal_width + 'px'});
        $('#content').css({'margin-top': MM_NORMAL_HEIGHT + 'px'});

        MM_FLOATING = true;

        mm_position();
      }
      else if(MM_FLOATING && y < MM_NORMAL_TOP)
      {
        $('#me').css({'position': 'static', 'width': ''});
        $('#content').css({'margin-top': '0'});

        MM_FLOATING = false;

        mm_position();
      }
    });
  }
});
