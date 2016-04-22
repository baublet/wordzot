<style type="text/css">

.wz-menu { display:flex; }
.wz-menu label {
  cursor: pointer;
  padding: .5rem;
  border: 1px solid rgba(0, 0, 0, .1);
  background: rgba(0, 0, 0, .01);
  border-radius: 5px;
}
.wz-menu label:hover {
  border-color: rgba(0,0,0,.5);
  background: rgba(0,0,0,.1);
}
.wz-menu li + li {
  padding-left: .5rem;
}

.wz-helper-input {display: none !important;}
.wz-helper-input + .wordzot-wrapper-pane {display: none;}
.wz-helper-input:checked + .wordzot-wrapper-pane {display: block;}

.wz-emergency {margin-top:50vh;background:#B71C1C;color:#eee;padding:1rem;border-radius:9px;}
.wz-emergency h2{color:#eee;margin:0;padding:0;margin-bottom:1rem;}

.wz-subtemplates {
  margin: 0;
  padding: 0;
  margin-right: 2rem;
  height: 100%;
  max-height: 350px;
  overflow-x: hidden;
  overflow-y: auto;
  border: 1px solid #ddd;
  box-shadow: inset 0 0 3px 3px rgba(0,0,0,.075);
  width: 15rem;
}
.wz-subtemplates li:nth-child(even) label {background: #eee;}
.wz-subtemplates label {padding: .5rem 2rem .5rem 1rem;display: block;}
.wz-subtemplates li label:hover {background: #ddd;}
.wz-subtemplates li {margin:0;padding:0;}
.wz-subtemplates li + li label {border-top: 1px solid #ddd;}
.wz-edit-templates{width:100%;}
.wz-edit-templates .wordzot-wrapper-pane textarea {height:300px;width:100%;}

</style>

<?php foreach($this->errors as $error): ?>
  <div class="error">
    <p><strong>Error:</strong> <?php echo $error; ?></p>
  </div>
<?php endforeach; ?>

<?php foreach($this->successes as $success): ?>
  <div class="updated">
    <p><strong>Success:</strong> <?php echo $success; ?></p>
  </div>
<?php endforeach; ?>

<div class="wordzot wrap">
  <h2>WordZot</h2>
  <?php include("_navigation.php"); ?>
