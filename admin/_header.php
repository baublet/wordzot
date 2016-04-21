<style type="text/css">

.wz-menu { display:flex; }
.wz-menu label {
  cursor: pointer;
  padding: .5rem;
  border: 1px solid transparent;
  border-radius: 5px;
}
.wz-menu label:hover {
  border-color: rgba(0,0,0,.5);
  background: rgba(0,0,0,.1);
}
.wz-menu li + li {
  padding-left: .5rem;
}

.wz-helper-input {
  display: none !important;
}

.wz-helper-input + .wordzot-wrapper-pane {
  display: none;
}

.wz-helper-input:checked + .wordzot-wrapper-pane {
  display: block;
}

</style>

<?php foreach($this->errors as $k => $error): ?>
  <div class="error">
    <p><strong>Error:</strong> <?php echo $error; ?></p>
  </div>
<?php endforeach; ?>

<?php foreach($this->success as $k => $success): ?>
  <div class="updated">
    <p><strong>Success:</strong> <?php echo $success; ?></p>
  </div>
<?php endforeach; ?>

<div class="wordzot wrap">
  <h2>WordZot</h2>
  <?php include("_navigation.php"); ?>
