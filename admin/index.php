<?php
  // Updates our API key if a new one is passed
  if($_POST["apikey"]) {
    update_option("wordzot-api-key", $_POST["apikey"]);
    $wordzot_base->initialize();
    $wordzot_base->phpZot->testConnection();
  }
?>

<?php include("_header.php"); ?>

<h3>Zotero Settings</h3>
<?php if (!get_option("wordzot-user-id")): ?>
  <p class="error"><strong>Error:</strong> To unlock the WordZot settings, you
  must enter a valid API key in the field below and click "Save Changes." If
  your API key is valid, the options will be unlocked and this message will
  no longer be present.</p>
<?php else: ?>
  <p class="success"><strong>Your API Key is Valid!</strong> You may now use
  all of WordZot and its features with proper configuration.</p>
<?php endif; ?>
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
