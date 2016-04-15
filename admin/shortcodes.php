<?php $this->requireAPIKey(); ?>
<?php include("_header.php"); ?>
<h3>Shortcodes</h3>
<p>Here, you can peruse your library, browse your shortcodes, and examine how they're rendered.</p>
<hr>

<h3>Available Groups</h3>
<?php $groups = $this->wz->phpZot->getGroups(); ?>

<table>
  <tr>
    <th>id</th>
    <th>Group Name</th>
    <th>Items</th>
    <th>Description</th>
    <th>Example Useage</th>
  </tr>
  <tbody>
  <?php foreach($groups as $group): ?>
    <tr>
      <td><?php echo $group->id; ?></td>
      <td>
        <strong>
          <a href="<?php echo $group->links->alternate->href; ?>">
            <?php echo $group->data->name; ?>
          </a>
        </strong>
      </td>
      <td><?php echo $group->meta->numItems; ?></td>
      <td><?php echo $group->data->description; ?></td>
      <td>[wordzot group=<?php echo $group->id; ?>]</td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php include("_footerphp"); ?>
