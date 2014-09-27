<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

$books = array(
  'The Hound of the Baskervilles: Chapter 1',
  'The Hound of the Baskervilles: Chapter 2',
  'The Hound of the Baskervilles: Chapter 3',
  'The Hound of the Baskervilles: Chapter 4',
  'The Hound of the Baskervilles: Chapter 5',
  'The Hound of the Baskervilles: Chapter 6',
  'The Hound of the Baskervilles: Chapter 7',
  'The Hound of the Baskervilles: Chapter 8',
  'The Hound of the Baskervilles: Chapter 9',
);

$book = $books[array_rand($books)];

echo '<p>The stick vanishes, in its place a fresh copy of ' . $book . '!</p>';

add_inventory($user['user'], '', $book, '', $this_inventory['location']);
?>
