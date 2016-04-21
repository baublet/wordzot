<?php include("_header.php"); ?>

<div style="display:flex">
  <div style="width: 60%; padding-right: 2rem;">
    <h3>Templates</h3>
    <p>Templates are where this plugin shines. You cannot delete the default template,
      but you can edit it, and create and remove custom templates of your own! This
      feature allows you to display your citations in any format you would like, using
      <a href="http://twig.sensiolabs.org/">Twig's</a> curly braces format and any custom
      style ements or inline code you need to control how your citations look.</p>
  </div>
  <div style="width:40%; padding-left: 2rem; padding-top: 2rem; border-left:1px solid rgba(0,0,0,.075)">
    <form action="" method="POST">
      <input type="hidden" name="new-template-group" value="true">
      <h4><label for="new-tg">New Template Group:</label></h4>
      <input type="text" name="new-tg-name" id="new-tg"
        placeholder="Template Group Unique Name" style="width: 100%">
      <input type="submit" value="New Template Group" class="button" style="float:right;margin-top:.5rem;">
    </form>
  </div>
</div>

<hr>

<form action="" method="POST">
  <input type="hidden" name="save-templates" value="true">
  <ul class="wz-menu">
    <?php foreach($templates as $template): ?>
    <li>
      <label for="wz_<?php echo $template["slug"]; ?>">
        <strong><?php echo $template["name"]; ?></strong>
      </label>
    </li>
    <?php endforeach; ?>
  </ul>

  <hr>

  <?php $first = true; foreach($templates as $template): ?>
  <input type="radio" class="wz-helper-input"
    id="wz_<?php echo $template["slug"]; ?>"
    <?php echo ($first) ? " checked=\"checked\"" : '' ;
    $first = false; ?>
    name="wz-template-groups">
  <div class="wordzot-wrapper-pane">
    <h3><?php echo $template["name"]; ?></h3>
    More here later ;)
  </div>
  <?php endforeach; ?>

</form>


<?php include("_footer.php"); ?>
