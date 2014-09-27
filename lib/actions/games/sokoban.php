<?php
if($okay_to_be_here !== true)
  exit();

if($user['idnum'] == 1)
{
?>
<script type="text/javascript">
 $(function()
 {
   var map = new Array();

   map[0] = new Array(1, 1, 1, 1, 0, 0, 0, 0);
   map[1] = new Array(1, 0, 0, 1, 1, 1, 1, 1);
   map[2] = new Array(1, 0, 2, 0, 0, 3, 0, 1);
   map[3] = new Array(1, 0, 3, 0, 0, 2, 4, 1);
   map[4] = new Array(1, 1, 1, 0, 0, 1, 1, 1);
   map[5] = new Array(0, 0, 1, 1, 1, 1, 0, 0);

   var player_pos = new Array(0, 0);

   $('#sokoban').height(map.length * 16);

   for(i = 0; i < map.length; i++)
   {
     for(j = 0; j < map[i].length; j++)
     {
       real_x = j * 16 + $('#sokoban').offset().left;
       real_y = i * 16 + $('#sokoban').offset().top;
     
       switch(map[i][j])
       {
         case 1:
           $("#sokoban").append('<div class="wall" style="top:' + real_y + 'px;left:' + real_x + 'px"></div>');
           break;
         case 2:
           $("#sokoban").append('<div class="goal" style="top:' + real_y + 'px;left:' + real_x + 'px"></div>');
           break;
         case 3:
           $("#sokoban").append('<div id="c' + i + '_' + j + '" class="crate" style="z-index:1000;top:' + real_y + 'px;left:' + real_x + 'px"></div>');
           break;
         case 4:
           map[i][j] = 0;
           player_pos = new Array(i, j);
           $("#sokoban").append('<div id="player" style="z-index:1000;top:' + real_y + 'px;left:' + real_x + 'px"></div>');
           break;
         case 5:
           $("#sokoban").append('<div class="goal" style="top:' + real_y + 'px;left:' + real_x + 'px"></div>');
           $("#sokoban").append('<div id="c' + i + '_' + j + '" class="crate" style="z-index:1000;top:' + real_y + 'px;left:' + real_x + 'px"></div>');
           break;
         case 6:
           map[i][j] = 2;
           player_pos = new Array(i, j);
           $("#sokoban").append('<div class="goal" style="top:' + real_y + 'px;left:' + real_x + 'px"></div>');
           $("#sokoban").append('<div id="player" style="z-index:1000;top:' + real_y + 'px;left:' + real_x + 'px"></div>');
           break;
       }
     }
   }

   function possible_move(x, y)
   {
     var tile = map[player_pos[0] + y][player_pos[1] + x];
     var far_tile = map[player_pos[0] + 2 * y][player_pos[1] + 2 * x];
     var can_move = false;
     var solved = true;

     if(tile == 0 || tile == 2)
     {
       can_move = true;
       solved = false;
     }
     else
     {
       if((tile == 3 || tile == 5) && (far_tile == 0 || far_tile == 2))
       {
         map[player_pos[0] + y][player_pos[1] + x] -= 3;
         map[player_pos[0] + 2 * y][player_pos[1] + 2 * x] += 3;
         $('#c' + (player_pos[0] + y) + '_' + (player_pos[1] + x)).animate(
           { left: '+=' + (x * 16), top: '+=' + (y * 16) },
           100,
           function() {
             for(i = 0; i < map.length; i++)
             {
               for(j = 0; j < map[i].length; j++)
               {
                 if(map[i][j] == 2)
                 {
                   solved = false;
                   break;
                 }
               }
             }

             if(solved)
             {
               alert("SOLVED");
             }
           }
         ).attr('id','c' + (player_pos[0] + 2 * y) + '_' + (player_pos[1] + 2 * x));
         can_move = true;
       }
     }
     if(can_move)
     {
       player_pos[0] += y;
       player_pos[1] += x;
       $('#player').animate({left: '+=' + (x * 16), top: '+=' + (y * 16)}, 100);
     }
   }

	 $(document).keydown(function(event)
   {
	   switch(event.keyCode) {
       case 65:
         possible_move(-1, 0);
         break;
       case 87:
         possible_move(0, -1);
         break;
		   case 68:
         possible_move(1, 0);
         break;
       case 83:
         possible_move(0, 1);
         break;
     }
   });
 });
</script>
<style type="text/css">
 #sokoban
 {
 	 background-color:#9cf;
 }

 .wall
 {
 	 background-color:#666;
 	 width:16px;
	 height:16px;
 	 position:absolute;
 }

 .goal
 {
 	 background-color:#fff;
 	 width:16px;
 	 height:16px;
 	 position:absolute;
 }

 .crate
 {
 	 width:16px;
 	 height:16px;
 	 position:absolute;
 }

 #player
 {
   width:16px;
   height:16px;
   position:absolute;
 }
</style>
<p class="failure">MAKE SURE THIS IS AVAILABLE TO PEOPLE BEFORE POSTING ABOUT IT! >_></p>
<div id="sokoban"></div>
<h5>How To Play</h5>
<p>You are the Tiny Elephant. Move with the arrow keys.</p>
<p>The object is to get all of the blue blocks into the white blocks.</p>
<p>Push the blue blocks around by moving into them, but be careful: you cannot pull blocks, and you cannot push two in a row.</p>
<p>It can require some thought! You might not get it the first time around.</p>
<?php
}
else
  echo '<p>The original Sokoban web game has been taken off-line!  I\'m working on creating my own from scratch.</p>';
?>