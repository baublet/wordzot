<?php

namespace Zotero;

const ZOTERO_API_URI = "https://api.zotero.org";
const ZOTERO_API_VERSION = 3;

// My own class for the Zotero API because libZotero isn't a good fit for my needs

class phpZot {
  private $api_key;
  public function __construct($api_key) {
    $this->api_key = $api_key;
  }

  public function request($url) {
    $ch = curl_init();
    $http_headers = array(
      "Zotero-API-Version: " . ZOTERO_API_VERSION,
      "User-Agent: API",
      "Expect:"
    );
    $url =  ZOTERO_API_URI . $url;

    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPGET, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
  }

  public function testConnection() {
    $response = json_decode($this->request("/keys/" . $this->api_key));
    if($response->key == $this->api_key) {
      update_option("wordzot-user-id", $response->userID);
      update_option("wordzot-username", $response->username);
      return true;
    }
    update_option("wordzot-user-id", false);
    update_option("wordzot-username", false);
    return false;
  }

  public function getGroups() {
    $response = json_decode(
      $this->request(
        "/users/" . get_option("wordzot-user-id") . "/groups"
      )
    );
    return $response;
  }
}
