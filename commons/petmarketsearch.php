    <h5>Search</h5>
<?php if($search_message) echo '<p>' . $search_message . '</p>'; ?>
    <form action="petmarket.php?page=<?= $page ?>" method="get">
    <table border="0" cellpadding="0" cellspacing="4" class="nomargin">
     <tr>
      <th>Owner:</th><td colspan="2"><input name="owner" value="<?= $owner ?>" /></td><td>(optional)</td>
     </tr>
     <tr>
      <th>Level:</th><td colspan="2"><input maxlength="3" size="3" name="minlevel" value="<?= $minlevel ?>" /> - <input maxlength="3" size="3" name="maxlevel" value="<?= $maxlevel ?>" /></td><td>(optional)</td>
     </tr>
     <tr>
      <th>Gender:</th><td><input type="radio" name="gender" value="any"<?= $gender == 'any' ? ' checked' : '' ?> /> Either</td><td><input type="radio" name="gender" value="male"<?= $gender == 'male' ? ' checked' : '' ?> /> Male</td><td><input type="radio" name="gender" value="female"<?= $gender == 'female' ? ' checked' : '' ?> /> Female</td>
     </tr>
     <tr>
      <th>Fixed:</th><td><input type="radio" name="prolific" value="any"<?= $prolific == 'any' ? ' checked' : '' ?> /> Either</td><td><input type="radio" name="prolific" value="no"<?= $prolific == 'no' ? ' checked' : '' ?> /> Yes</td><td><input type="radio" name="prolific" value="yes"<?= $prolific == 'yes' ? ' checked' : '' ?> /> No</td>
     </tr>
    </table>
    <p><input type="hidden" name="action" value="search" /><input type="submit" value="Search" /></p>
    </form>
    <h5>Listing</h5>
