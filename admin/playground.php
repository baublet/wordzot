<?php include("_header.php"); ?>

<h3>Playground</h3>
<p>Here, you can test WordZot shortcodes and examine how they display. Enter a
  shortcode or shortcodes in the box below and press the button below.</p>
<table>
  <tr>
    <td width="50%" style="padding-right: 1rem;">
      <form action="" method="POST">
        <textarea style="width: 100%;height: 200px" name="parse"><?php echo (isset($_POST["parse"])) ? $_POST["parse"]: get_option("wordzot-playground");?></textarea>
        <?php submit_button('Preview Shortcode', 'primary','submit', TRUE); ?>
      </form>
    </td>
    <td width="50%" valign="top">
      <h4>Help</h4>
      <p>WordZot's basic shortcode is [wordzot]. This will show all items in the current user's library. To narrow it down, use some of the following options:</p>
      <ul>
        <li><em>group</em> - the ID of the group whose collections you wish to draw from.</li>
        <li><em>collection</em> - the Key of the collection you wish to draw from.</li>
        <li><em>tags</em> - the string of the tags you wish to draw from.</li>
      </ul>
    </td>
  </tr>
</table>

<?php if($output !== false): ?>
  <hr>
  <h3>Output</h3>

  <div style="padding:.5rem;border:1px solid #999;background: #fcfcfc;max-height:500px;overflow:hidden;overflow-y:auto;">
    <?php echo $output; ?>
  </div>

<?php endif; ?>

<?php include("_footer.php"); ?>
