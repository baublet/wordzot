<?php

class WordZot {
  public function __construct($twig) {
    $this->twig = $twig;
  }

  public function install() {
    register_setting("wordzot", "api_key");
  }

  public function uninstall() {
    unregister_setting("wordzot", "api_key");
  }
}
