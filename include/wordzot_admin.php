<?php

class WordZotAdmin {

  public $admin_dir = false;

  private $twig, $wz;

  public  $successes = null,
          $errors = null;

  public function __construct($twig, $wordzot) {
    $this->twig = $twig;
    $this->wz = $wordzot;
    add_action("admin_menu", array($this, "create_menu"));
    $this->successes = array();
    $this->errors = array();
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

        add_submenu_page(
          "wordzot",
          "Templates",
          "Templates",
          "manage_options",
          "wordzot-templates",
          array($this, "showTemplates"));

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

  public function error($message) {
    \WordZot::log("Error: " . $message);
    $this->errors[] = $message;
  }

  public function success($message) {
    \WordZot::log("Success: " . $message);
    $this->successes[] = $message;
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

    if (!get_option("wordzot-user-id")) {
      $this->error("To unlock the WordZot settings, you
      must enter a valid API key in the field below and click \"Save Changes.\" If
      your API key is valid, the options will be unlocked and this message will
      no longer be present.");
    } else {
      $this->success("<em>Your API Key is Valid!</em> You may now use
      all of WordZot and its features with proper configuration.");
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
      $output = do_shortcode(stripslashes($_POST["parse"]));
    }

    include($this->admin_dir . "playground.php");
  }

  private function templatesDeleteGroup() {
    /* Delete a template group */
    if(isset($_GET["delete"])) {
      $slug = $_GET["delete"];
      $templates = get_option("wordzot-templates");
      if($slug !== "default" && isset($templates[$slug])) {
        unset($templates[$slug]);
        update_option("wordzot-templates", $templates);
        $this->success("Template <em>" . $slug . "</em> successfully deleted");
      } else {
        $this->error("Unable to delete template <em>" . $slug . "</em>");
      }
    }
  }

  private function templatesSaveData() {
    if(!isset($_POST["save-template-data"])) return;
    $templates = $_POST["wz_tpl"];
    //\WordZot::log("Passed templates:" . print_r($templates, true));
    update_option("wordzot-templates", $templates);
    $this->success("Templates successfully saved!");
  }

  private function templatesAddGroup() {
    /* Add a new template group */
    if($_POST["new-template-group"] == true) {
      $ntg_name = stripslashes($_POST["new-tg-name"]);
      $ntg_slug = sanitize_title_with_dashes($ntg_name);
      \WordZot::log("New template group slug: " . $ntg_slug);
      if(!empty($ntg_slug)) {
        $templates = get_option("wordzot-templates");
        if(!isset($templates[$ntg_slug])) {
          $ntg_templates = $this->wz->starter_templates["default"]["templates"];
          \WordZot::log("New template starters: \n" . print_r($ntg_templates, true));
          $templates[$ntg_slug] = array(
            "slug" => $ntg_slug,
            "name" => $ntg_name,
            "templates" => $ntg_templates
          );
          update_option("wordzot-templates", $templates);
          \WordZot::log("New template added: " . $ntg_name . " (" . $ntg_slug . ")");
          $this->success("New template <em>" . $ntg_name . "</em> successfully added");
        } else {
          $this->error("New template name is too similar to existing template: <em>" .
                                $templates[$ntg_slug]["name"] .
                                "</em>. Template names should be as unique as possible");
        }
      } else {
        $this->error("New template name cannot be blank");
      }
    }
  }

  private function templatesResetAll() {
    if(!isset($_POST["reset-template-data"])) return;
    update_option("wordzot-templates", $this->wz->starter_templates);
    $this->success("Your templates have been set to plugin defaults");
  }

  public function showTemplates() {

    $this->templatesSaveData();
    $this->templatesAddGroup();
    $this->templatesDeleteGroup();
    $this->templatesResetAll();

    /* Display our templates */
    $templates = get_option("wordzot-templates");
    //\WordZot::log("Templates: \n" . print_r($templates, true));
    //\WordZot::log("Admin Object: \n" . print_r($this, true));
    include($this->admin_dir . "templates.php");
  }

}
