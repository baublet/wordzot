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

        add_submenu_page(
          "wordzot",
          "Playground",
          "Playground",
          "manage_options",
          "wordzot-playground",
          array($this, "showPlayground"));

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
    // If the user submitted new data, load it up
    if($_POST["apikey"]) {
      update_option("wordzot-api-key", $_POST["apikey"]);
      $this->wz->initialize();
      $response = $this->wz->phpZot->testConnection();
      if($response !== false) {
        update_option("wordzot-user-id", $response->userID);
        update_option("wordzot-username", $response->username);
      } else {
        update_option("wordzot-user-id", false);
        update_option("wordzot-username", false);
      }
    }
    include($this->admin_dir . "index.php");
  }

  public function showShortcodes() {
    $this->requireAPIKey();
    // Load all of our data to be rendered by the template
    $collections = $this->wz->phpZot->getUserCollections(get_option("wordzot-user-id"));
    $groups = $this->wz->phpZot->getUserGroups(get_option("wordzot-user-id"));
    $tags = $this->wz->phpZot->getUserTags(get_option("wordzot-user-id"));

    \WordZot::log("Our tags:");
    \WordZot::log($tags);

    include($this->admin_dir . "shortcodes.php");
  }

  public function showPlayground() {
    $this->requireAPIKey();

    $output = false;
    // Parse the data they submitted to be parsed via the shortcode parser
    if($_POST["parse"]) {
      update_option("wordzot-playground", $_POST["parse"]);
      $output = do_shortcode($_POST["parse"]);
    }

    include($this->admin_dir . "playground.php");
  }

}
