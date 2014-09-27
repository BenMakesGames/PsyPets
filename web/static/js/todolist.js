function todo_vote(id, vote)
{
  $('#vote' + id).html('<img src="gfx/throbber.gif" alt="saving..." />');

  $.ajax({
    type: 'GET',
    url: 'ajax_vote_on_todo_list.php',
    data: 'itemid=' + id + '&vote=' + vote,
    success: todo_vote_success
  });

}

function todo_vote_success(msg)
{
  var response = msg.split("\n", 2);

  $('#vote' + response[0]).html(response[1]);
}
