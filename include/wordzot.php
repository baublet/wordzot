<?php

class WordZot {
  public $phpZot;
  private $api_key = false;

  public $starter_templates = array(
    "default" => array(
      "slug" => "default",
      "name" => "Default Template",
      "templates" => array(
        "default" => "{{ author }}, <em>{{ title }}</em> ({{ date }})",
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
        "dictionaryEntry" => ""
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
