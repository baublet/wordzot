<?php
include("_header.php");

// This is a utility function I'm using to show the collections tree
function wz_showCollectionChildren($collection) {
  echo "<ul style=\"padding-left:1.25rem;margin-top:.5rem;list-style-type:disc;\">";
  foreach($collection->children as $child): ?>
    <li>
      <strong>
        <a href="<?php echo $child->url; ?>">
          <?php echo $child->name; ?>
        </a>
      </strong>
      <small style="opacity: .75; font-size: 75%"><?php echo $child->key; ?></small>
      <?php if(count($child->children) > 0) wz_showCollectionChildren($child); ?>
    </li>
  <?php endforeach;
  echo "</ul>";
}
?>

<h3>Shortcodes</h3>
<p>Here, you can peruse your library, browse your shortcodes, and examine how they're rendered.</p>
<hr>

<ul class="wz-menu">
  <li>
    <label for="wz_collections"><strong>Collections</strong> (<?php echo count($collections); ?>)</label>
  </li>
  <li>
    <label for="wz_groups"><strong>Groups</strong> (<?php echo count($groups); ?>)</label>
  </li>
  <li>
    <label for="wz_tags"><strong>Tags</strong> (<?php echo count($tags); ?>)</label>
  </li>
</ul>

<input type="radio" checked="checked" class="wz-helper-input" name="wz_sc_tabs" id="wz_collections">
<div class="wordzot-wrapper-pane">
  <h3>Collections</h3>

  <?php if($collections !== false): ?>
    <table class="wp-list-table widefat fixed striped">
      <thead>
        <tr>
          <th width="10%">id</th>
          <th width="50%">Collection Name</th>
          <th width="10%">Items</th>
          <th width="30%">Example Useage</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($collections as $collection): ?>
          <tr>
            <td><?php echo $collection->key; ?></td>
            <td>
              <strong>
                <a href="<?php echo $collection->url; ?>">
                  <?php echo $collection->name; ?>
                </a>
              </strong>
              <?php if(count($collection->children) > 0): ?>
                <?php echo wz_showCollectionChildren($collection);?>
              <?php endif; ?>
            </td>
            <td><?php echo $collection->numItems; ?></td>
            <td>[wordzot collection=<?php echo $collection->key; ?>]</td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>You have no collections to draw from.</p>
  <?php endif; ?>

</div>

<input type="radio" class="wz-helper-input" name="wz_sc_tabs" id="wz_groups">
<div class="wordzot-wrapper-pane">
  <h3>Groups</h3>

  <?php if($groups !== false): ?>

    <table class="wp-list-table widefat fixed striped">
      <thead>
        <tr>
          <th width="10%">id</th>
          <th width="50%">Group Name</th>
          <th width="10%">Items</th>
          <th width="30%">Example Useage</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($groups as $group): ?>
        <tr>
          <td><?php echo $group->id; ?></td>
          <td>
            <strong>
              <a href="<?php echo $group->url; ?>">
                <?php echo $group->name; ?>
              </a>
            </strong>
            <p><?php echo $group->description; ?></p>
          </td>
          <td><?php echo $group->numItems; ?></td>
          <td>[wordzot group=<?php echo $group->id; ?>]</td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

  <?php else: ?>
    <p>You have no groups to pull from.</p>
  <?php endif; ?>

</div>

<input type="radio" class="wz-helper-input" name="wz_sc_tabs" id="wz_tags">
<div class="wordzot-wrapper-pane">
  <h3>Tags</h3>

  <?php if($tags !== false): ?>

    <table class="wp-list-table widefat fixed striped">
      <thead>
        <tr>
          <th width="50%">Tag Name</th>
          <th width="10%">Items</th>
          <th width="30%">Example Useage</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($tags as $tag): ?>
          <tr>
            <td>
              <strong>
                <a href="<?php echo $tag->url; ?>">
                  <?php echo $tag->name; ?>
                </a>
              </strong>
            </td>
            <td><?php echo $tag->numItems; ?></td>
            <td>[wordzot tag="<?php echo $tag->name; ?>"]</td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  <? else: ?>
    <p>You don't have any tags at the moment.</p>
  <?php endif; ?>

</div>

<?php include("_footer.php"); ?>
