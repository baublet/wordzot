<?php
$page = (isset($_GET['page'])) ? $_GET['page'] : false;
?>

<h2 class="nav-tab-wrapper">
  <a href="?page=wordzot"
     class="nav-tab<?php echo ($page == "wordzot") ? " nav-tab-active" : ""; ?>">
    General
  </a>
  <a href="?page=wordzot-shortcodes"
     class="nav-tab<?php echo ($page == "wordzot-shortcodes") ? " nav-tab-active" : ""; ?>">
    Shortcodes
  </a>
  <a href="?page=wordzot-playground"
     class="nav-tab<?php echo ($page == "wordzot-playground") ? " nav-tab-active" : ""; ?>">
    Playground
  </a>
  <a href=""
     class="nav-tab<?php echo ($page == "") ? " nav-tab-active" : ""; ?>">
    User Submissions
  </a>
  <a href="?page=wordzot%2Fadmin%2Findex.php"
     class="nav-tab<?php echo ($page == "") ? " nav-tab-active" : ""; ?>">
    Cache
  </a>
</h2>
