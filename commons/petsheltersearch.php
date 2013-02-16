    <h5>Search</h5>
    <form action="petshelter.php?page=<?= $page ?>" method="get">
    <table class="nomargin">
     <tr>
      <th>Level:</th><td colspan="2"><input maxlength="3" size="3" name="minlevel" value="<?= $minlevel ?>" /> - <input maxlength="3" size="3" name="maxlevel" value="<?= $maxlevel ?>" /></td><td>(optional)</td>
     </tr>
     <tr>
      <th>Gender:</th><td><input type="radio" name="gender" value="any"<?= $gender == 'any' ? ' checked' : '' ?> /> Either</td><td><input type="radio" name="gender" value="male"<?= $gender == 'male' ? ' checked' : '' ?> /> Male</td><td><input type="radio" name="gender" value="female"<?= $gender == 'female' ? ' checked' : '' ?> /> Female</td>
     </tr>
<?php
if($whereat != 'petshelter')
{
?>
     <tr>
      <th>Fixed:</th><td><input type="radio" name="prolific" value="any"<?= $prolific == 'any' ? ' checked' : '' ?> /> Either</td><td><input type="radio" name="prolific" value="no"<?= $prolific == 'no' ? ' checked' : '' ?> /> Yes</td><td><input type="radio" name="prolific" value="yes"<?= $prolific == 'yes' ? ' checked' : '' ?> /> No</td>
     </tr>
<?php
}
?>
    </table>
    <p><input type="hidden" name="action" value="search" /><input type="submit" value="Search" /></p>
    </form>
    <h5>Listing</h5>
