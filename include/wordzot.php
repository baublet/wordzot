<?php

class WordZot {
  public $phpZot;
  private $api_key = false;

  public function __construct($twig) {
    $this->twig = $twig;
    $this->initialize();
  }

  public function install() {

  }

  public function uninstall() {

  }

  public function initialize() {
    $api_key = get_option("wordzot-api-key");
    if($api_key === false) return false;

    // Initialize our phpZot class(es)
    $this->api_key = $api_key;
    $this->phpZot = new \Zotero\phpZot($this->api_key);

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
   * Our shortcode handling function.
   */
  public function do_shortcode($atts) {
    $options = shortcode_atts(array(
        'collection' => false,
        'group' => false,
        'tags' => false,
        'tag' => false,
        'limit' => false,
        'sort' => false,
        'order' => false,
        'direction' => false,
        'offset' => 0,
        'paginate' => false,
    ), $atts);

    $content = '';

    if(get_option("wordzot-user-id") == false) return "Incorrect or unset Zotero API key.";

    $content = $this->phpZot->getUserItems(get_option("wordzot-user-id"));

    return print_r($content, true);
  }
}
