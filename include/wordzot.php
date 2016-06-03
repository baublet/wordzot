<?php

class WordZot {
  public $phpZot, $twig;
  private $api_key = false;

  public function __construct($twig) {
    $this->twig = $twig;
    $this->initialize();
    include(_WORDZOT_PLUGIN_DIR . "/include/default_templates.php");
    $this->starter_templates = $starter_templates;
  }

  public function install() {
    update_option("wordzot-user-id", false);
    update_option("wordzot-username", false);
    update_option("wordzot-playground", false);
    update_option("wordzot-templates", $this->starter_templates);
  }

  public function uninstall() {
    delete_option("wordzot-user-id");
    delete_option("wordzot-username");
    delete_option("wordzot-playground");
    delete_option("wordzot-templates");
  }

  public function initialize() {
    $api_key = get_option("wordzot-api-key");
    if($api_key === false) return false;

    // Initialize our cache provider
    $cache_provider = new \Zotero\fsCache(_WORDZOT_PLUGIN_DIR . "/cache");

    // Initialize our phpZot class(es)
    $this->api_key = $api_key;
    $this->phpZot = new \Zotero\phpZot($this->api_key, $cache_provider);

    // Add our shortcode
    add_shortcode("wordzot", array($this, "do_shortcode"));
  }

  public static function apiKeyValid() {
    if(get_option("wordzot-user-id") == false) return false;
    return true;
  }

  public static function log($data) {
    if(_WORDZOT_DEBUG) {
          $output = "<script>console.log(`".
                    print_r($data, true) .
                    "`)</script>";
          echo $output;
    }
  }

  /*
   * This function returns a template preapred to be displayed in a form
   */
  public static function tplFormPrep($template) {
    return htmlentities(stripslashes($template));
  }

  /*
   * Our shortcode handling function.
   */
  public function do_shortcode($atts) {
    if(get_option("wordzot-user-id") == false) return "Incorrect or unset Zotero API key.";

    $options = shortcode_atts(array(
        "collection" => null,
        "group" => null,
        "tags" => null,
        "tag" => null,
        "limit" => 50,
        "sort" => false,
        "order" => false,
        "direction" => false,
        "offset" => 0,
        "paginate" => false,
        "template" => "default",
        "noitems" => "<p>No items have yet been added to this collection, group, or user ID.</p>",
        "type" => "-note"
    ), $atts);

    $php_zot_options = array();

    \WordZot::log("Tag options: " . print_r($options, true));
    $options["tags"] .= $options["tag"];
    $php_zot_options["tag"] = $options["tags"];
    $php_zot_options["itemType"]  = $options["type"];

    \WordZot::log("Tags: ". print_r($php_zot_options["tag"], true));

    // Sets our sort and sort order into a variable phpZot understands
    $php_zot_options["sort"] = $options["sort"];
    $php_zot_options["direction"] = ($options["direction"]) ? $options["direction"] : $options["order"];

    // Set our basic options
    $this->phpZot->resetOptions();
    $this->phpZot->setOptions($php_zot_options);

    // Get our items, preferring the user to the group
    $items = array();
    if($options["group"] == null) {
      $items = $this->phpZot->getUserItems(get_option("wordzot-user-id"));
    } else {
      $items = $this->phpZot->getGroupItems($options["group"]);
    }

    // Loop through our items to process the templates
    $content = "";
    foreach($items as $item) {
      $content .= $this->processTemplate($item->type, $options["template"], $item);
    }
    if(count($items) < 1) $content = $options["noitems"];
    if($this->phpZot->error) $content = "<p><strong>phpZot Error:</strong>" .
                                        " (" . $this->phpZot->error . ") " .
                                        $this->phpZot->error_message . "</p>";

    return $content;
  }

  /* Quick utility method for determining if a variable is blank */
  private function blank($var) {
    if ($var == null) return true;
    if(trim($var) == false) return true;
    if(empty($var)) return true;
    return false;
  }

  public function processTemplate($type, $template_group, $data) {
    $twig_template_name = "wordzot-" . $template_group . "-" . $type;
    // Only do this if we haven't gone through the steps already
    if(!$this->twig->getLoader()->exists($twig_template_name)) {
      // Cascade down the tree and find the correct template to use with $type
      if($this->templates == null) $this->templates = get_option("wordzot-templates");
      // Set our default as a blank template
      $template = "";
      // If the template group exists, use it, otherwise default to "default" group
      if(!isset($this->templates[$template_group])) $template_group = "default";
      $template = $this->templates[$template_group]["templates"][$type];
      // If the type doesn't exist here or is blank, use the default for this group
      if($this->blank($template)) $this->templates[$template_group]["templates"]["default"];
      // If the group's default is also empty, use the default template's $type
      if($this->blank($this->templates[$template_group]["templates"]["default"])) $template_group = "default";
      $template = $this->templates[$template_group]["templates"][$type];
      // If it's STILL empty, then we're going to use the default template group's default template
      if($this->blank($template)) $template = $this->templates["default"]["templates"]["default"] . "\n";

      // Set the template
      $this->twig->getLoader()->setTemplate($twig_template_name, $template);
    }

    // Now, let's turn $data into our twig_vars
    $twig_vars = array();
    $vars = get_object_vars($data);
    foreach($vars as $name => $value) {
      // We want to make separate arrays for creator types
      if($name == "creators") {
        foreach($value as $newcreator) {
          // Make the creator array
          $creator = array();
          $creator["firstName"] = $newcreator->firstName;
          $creator["lastName"] = $newcreator->lastName;
          $creator["fullName"] = $newcreator->firstName . " " . $newcreator->lastName;
          // Attach it to our creator type twig_var
          $twig_vars[$newcreator->creatorType . "s"][] = $creator;
        }
      } else {
        $twig_vars[$name] = $value;
      }
    }

    //\WordZot::log("Twig variables: " . print_r($twig_vars, true));

    // And render it!
    return $this->twig->render($twig_template_name, $twig_vars);
  }
}
