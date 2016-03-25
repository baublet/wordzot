<?php

class WordZotAdmin {

  private $twig;

  public function __construct($twig, $wordzot) {
    $this->twig = $twig;
    $this->wz = $wordzot;
    add_action('admin_menu', array($this, 'create_menu'));
  }

  public function create_menu() {
    add_menu_page (
            'WordZot',
            'WordZot',
            'manage_options',
            'wordzot/admin/index.php',
            '',
            '',
            '99'
        );
  }

}
