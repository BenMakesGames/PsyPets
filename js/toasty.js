var toasty = {
	controlHTML: '<img src="//saffron.psypets.net/gfx/npcs/toasty.png" style="width: 300px; height: 200px;" />',

	init: function()
  {
		jQuery(document).ready(
      function($)
      {
  		  var mainobj = toasty;

  			mainobj.$control = $('<div id="toasty">' + mainobj.controlHTML + '</div>')
  				.css({position: 'fixed', bottom: 0, right: 0, width: 0})
  				.appendTo('body');
  				
        mainobj.$control.animate(
          { width: 300 },
          500,
          function()
          {
            mainobj.$control.animate(
              { width: 300 },
              500,
              function()
              {
                mainobj.$control.animate(
                  { width: 0 },
                  333
                );
              }
            );
          }
        );
  		}
    )
	}
}

toasty.init()
