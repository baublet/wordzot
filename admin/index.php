<?php include("_header.php"); ?>

<h3>Zotero Settings</h3>

<form action="" method="POST">
  <table class="form-table">
    <tr>
      <th><label for="apikey">Zotero API Key:</label></th>
      <td>
        <input type="text" name="apikey" id="apikey"
          value="<?=get_option("wordzot-api-key")?>">
      </td>
    </tr>
    <tr>
      <th><label>User ID:</label></th>
      <td><input type="text" value="<?=get_option("wordzot-user-id")?>" disabled></td>
    </tr>
    <tr>
      <th><label>Username:</label></th>
      <td><input type="text" value="<?=get_option("wordzot-username")?>" disabled></td>
    </tr>
  </table>
  <p class="submit">
    <?php submit_button('Save Changes', 'primary','submit', TRUE); ?>
  </p>
</form>

<?php include("_footer.php");
