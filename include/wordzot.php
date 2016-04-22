<?php

class WordZot {
  public $phpZot, $twig;
  private $api_key = false;

  public $starter_templates = array(
    "default" => array(
      "slug" => "default",
      "name" => "Default Template",
      "templates" => array(
        "default" => "{% for author in authors %}{{ author.fullName }}, {% endfor %} <em>{{ title }}</em> ({{ date }})<br><br>",
        "note" => "",
        "book" => "",
        "bookSection" => "",
        "journalArticle" => "",
        "magazineArticle" => "",
        "thesis" => "",
        "letter" => "",
        "manuscript" => "",
        "interview" => "",
        "film" => "",
        "artwork" => "",
        "webpage" => "",
        "report" => "",
        "bill" => "",
        "case" => "",
        "hearing" => "",
        "patent" => "",
        "statute" => "",
        "email" => "",
        "blogPost" => "",
        "instantMessage" => "",
        "forumPost" => "",
        "audioRecording" => "",
        "presentation" => "",
        "videoRecording" => "",
        "tvBroadcast" => "",
        "radioBroadcast" => "",
        "podcast" => "",
        "computerProgram" => "",
        "document" => "",
        "encyclopediaArticle" => "",
        "dictionaryEntry" => "",
        "attachment" => ""
      )
    )
  );

  public function __construct($twig) {
    $this->twig = $twig;
    $this->initialize();
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
    $options = shortcode_atts(array(
        "collection" => false,
        "group" => false,
        "tags" => false,
        "tag" => false,
        "limit" => false,
        "sort" => false,
        "order" => false,
        "direction" => false,
        "offset" => 0,
        "paginate" => false,
        "template" => "default",
        "noitems" => "<p>No items have yet been added to this collection, group, or user ID.</p>",
        "type" => "-attachment"
    ), $atts);

    // Set the to_include and to_exclude options
    $types = explode(",", $options["type"]);
    $to_include = array();
    $to_exclude = array();
    foreach($types as $type) {
      if(substr($type, 0, 1) == "-") {
        $to_exclude[] = substr($type, 1);
      } else {
        $to_include[] = $type;
      }
    }

    $content = "";

    if(get_option("wordzot-user-id") == false) return "Incorrect or unset Zotero API key.";

    $items = $this->phpZot->getUserItems(get_option("wordzot-user-id"));

    foreach($items as $item) {
      // Skip this if there are $to_include types set and this item isn't one of them
      if(count($to_include) > 0) {
        if(!in_array($item->type, $to_include)) continue;
      }
      // Skip this item type if it's explicitly excluded
      if(in_array($item->type, $to_exclude)) continue;
      $content .= $this->processTemplate($item->type, $options["template"], $item);
    }

    // Loop through our items to process the templates
    \WordZot::log("Twig loader: " . print_r($this->twig->getLoader(), true));

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

    \WordZot::log("Twig variables: " . print_r($twig_vars, true));

    // And render it!
    return $this->twig->render($twig_template_name, $twig_vars);
  }
}
