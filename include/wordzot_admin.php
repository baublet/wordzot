<?php

class WordZotAdmin {

  public $admin_dir = false;

  private $twig, $wz;

  public function __construct($twig, $wordzot) {
    $this->twig = $twig;
    $this->wz = $wordzot;
    add_action("admin_menu", array($this, "create_menu"));
  }

  public function requireAPIKey() {
    if(!WordZot::apiKeyValid()) {
      header("Location: " . admin_url("admin.php?page=wordzot"));
      die();
    }
  }

  public function create_menu() {
    add_menu_page (
            "WordZot",
            "WordZot",
            "manage_options",
            "wordzot",
            array($this, "showIndex"),
            "",
            "99"
        );

        add_submenu_page(
          "wordzot",
          "Shortcodes",
          "Shortcodes",
          "manage_options",
          "wordzot-shortcodes",
          array($this, "showShortcodes"));

        /*
        add_submenu_page(
          string $parent_slug,
          string $page_title,
          string $menu_title,
          string $capability,
          string $menu_slug,
          callable $function = '');
          */
  }

  public function showIndex() {
    $this->_include("index.php");
  }

  public function showShortcodes() {
    $this->_include("shortcodes.php");
  }

  private function _include($file = "/") {
    include($this->admin_dir . $file);
  }

}
