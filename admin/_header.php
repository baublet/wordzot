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

<div class="wordzot wrap">
  <h2>WordZot</h2>
  <?php include("_navigation.php"); ?>
