<?php

namespace Zotero;

const ZOTERO_API_URI = "https://api.zotero.org";
const ZOTERO_API_VERSION = 3;

// My own class for the Zotero API because libZotero isn't a good fit for my needs

class phpZot {

  public  $error = false,
          $error_message = null;

  private $api_key;

  public function __construct($api_key) {
    $this->api_key = $api_key;
  }

  /*
   * Our basic, default API response function.
   */
  public function response($url) {
    $this->error = false;
    $this->error_emssage = null;
    $ch = curl_init();
    $http_headers = array(
      "Zotero-API-Version: " . ZOTERO_API_VERSION,
      "User-Agent: API",
      "Authorization: Bearer " . $this->api_key,
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

    if($response == "Forbidden") {
      $this->error = 403;
      $this->error_message = "Forbidden. Make sure your API key or User ID is correct.";
      return false;
    }

    $response = json_decode($response);

    return $this->parse($response);
  }

  /*
   * This function parses a response, because with this class I want very simple
   * functions. So if there's an error, this class stores that error, but returns
   * a false response.
   */
  private function parse($response) {
    if($response == null || empty($response)) {
      $this->error = 404;
      $this->error_message = "Not found. Please make sure the User or Group ID are correct.";
      return false;
    }
    return $response;
  }

  /*
   * Tests the API key entered in via the constructor. Note that this function
   * is never implicitly called. You should really only use this to test API
   * keys and get the User ID and Username associated with keys.
   */
  public function testConnection() {
    $response = $this->response("/keys/" . $this->api_key);
    if($response->key == $this->api_key) {
      return $response;
    }
    return false;
  }

  public function getUserGroups($user_id) {
    $response = $this->response(
      "/users/" .
      $user_id  .
      "/groups"
    );

    // Return false if there's no items to parse
    if($response == false) return false;

    // Parse the items so that we can use them easily
    $groups = array();
    foreach($response as $raw_group) {
      $group = new \StdClass();
      $group->name = $raw_group->data->name;
      $group->id = $raw_group->id;
      $group->url = $raw_group->links->alternate->href;
      $group->numItems = $raw_group->meta->numItems;
      $group->description = $raw_group->data->description;
      $groups[]  = $group;
    }
    return $groups;
  }

  /*
  ** This function takes a collections response from the Zotero API and parses it into
  ** something less verbose, making it easier to develop with.
  */
  private function parseCollections($response) {
    if($response == false) return false;

    // First, build our collections into a flat array
    $collection_associations = array();
    foreach($response as $raw_collection) {
      $collection = new \StdClass();
      $collection->name = $raw_collection->data->name;
      $collection->key = $raw_collection->key;
      $collection->url = $raw_collection->links->alternative->href;
      $collection->numItems = $raw_collection->meta->numItems;
      $collection->parent = $raw_collection->data->parentCollection;
      $collection->children = array();
      $collection_associations[$collection->key] = $collection;
    }

    // Now, build it into a multi-dimensional object
    $collections = array();
    foreach($collection_associations as $collection) {
      // Is this collection's parent on our array?
      if(isset($collection_associations[$collection->parent])) {
        // Cool, then attach a reference of this to the parent's children var
        $collection_associations[$collection->parent]->children[] = $collection;
      } else {
        // No? Then make this collection one of the root collections
        $collections[] = $collection;
      }
    }

    return $collections;
  }

  public function getUserCollections($user_id) {
    $response = $this->response(
      "/users/" .
      $user_id .
      "/collections"
    );
    return $this->parseCollections($response);
  }

  public function getGroupCollections($group_id) {
    return $this->response(
      "/groups/" .
      $group_id .
      "/collections"
    );
  }

  /*
  ** This function takes a tags response from the Zotero API and parses it into
  ** something less verbose, making it easier to develop with.
  */
  private function parseTags($response) {
    if($response == false) return false;

    $tags = array();
    foreach($response as $raw_tag) {
      $tag = new \StdClass();
      $tag->name = $raw_tag->tag;
      $tag->tag = $tag->name;
      $tag->url = $raw_tag->links->alternate->href;
      $tag->numItems = $raw_tag->meta->numItems;
      $tags[] = $tag;
    }

    return $tags;
  }

  public function getUserTags($user_id) {
    $response = $this->response(
      "/users/" .
      $user_id .
      "/tags"
    );
    return $this->parseTags($response);
  }

  public function getGroupTags($group_id) {
    $response = $this->response(
      "/groups/" .
      $group_id .
      "/tags"
    );
    return $this->parseTags($response);
  }

}
