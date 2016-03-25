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

    $this->api_key = $api_key;
    $this->phpZot = new \Zotero\phpZot($this->api_key);
  }
}
