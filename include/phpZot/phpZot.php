<?php

namespace Zotero;

const ZOTERO_API_URI = "https://api.zotero.org";
const ZOTERO_API_VERSION = 3;

// My own class for the Zotero API because libZotero isn't a good fit for my needs

class phpZot {

  public  $error = false,
          $error_message = null,
          $cache_provider = null,
          $options = array(),
          $sortOptions = array(
            "dateAdded",
            "dateModified",
            "title",
            "creator",
            "type",
            "date",
            "publisher",
            "publicationTitle",
            "journalAbbreviation",
            "language",
            "accessDate",
            "libraryCatalog",
            "callNumber",
            "rights",
            "addedBy",
            "numItems"
          );

  private $api_key;

  public function __construct($api_key, $cache_provider = false) {
    $this->api_key = $api_key;
    $this->cache_provider = ($cache_provider) ? $cache_provider : null;
  }

  /*
   * Our basic, default API request function.
   */
  public function request($url) {
    // If this is cached, return the cached value instead
    $cache_key = md5($url . serialize($this->options));
    if($this->cache_provider !== null) {
      $cached = $this->cache_provider->get($cache_key);
      if($cached !== false) {
        return $cached;
      }
    }

    $this->error = false;
    $this->error_message = null;
    $ch = curl_init();
    $http_headers = array(
      "Zotero-API-Version: " . ZOTERO_API_VERSION,
      "User-Agent: API",
      "Authorization: Bearer " . $this->api_key,
      "Expect:"
    );
    $url =  substr(ZOTERO_API_URI . $url . $this->get_options_string(), 0, -1);

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

    // Save it to our cache
    if($this->cache_provider !== null) {
      $block = $response;
      $this->cache_provider->set($cache_key, $block);
    }

    return $this->parse($response);
  }

  /*
   * This function adds the options to the request URL string
   */
  private function get_options_string() {
    $string = "?";
    // First, parse the things that use Zotero's Search Syntax
    // https://www.zotero.org/support/dev/web_api/v3/basics#search_syntax
    if(isset($this->options["itemType"]))
      $string .= $this->gos_search_syntax($this->options["itemType"], "itemType");
    if(isset($this->options["tag"]))
      $string .= $this->gos_search_syntax($this->options["tag"], "tag");
    // Sort order operators
    $string .= $this->gos_sort_order();
    return $string;
  }

  /*
   * A utility function turning $data into a URL string with search syntax intact
   */
  private function gos_search_syntax($data, $type) {
    if(empty($data)) return "";
    $string = "";
    if(is_array($data)) {
      // Allowing recursion
      foreach($data as $item)
        $string .= $this->gos_search_syntax($item, $type);
    } else {
      $items = explode("||", $data);
      $string.= $type . "=";
      // Only URL encode the tag names themselves, not the bars or the minus sign
      foreach($items as &$item) {
        $negate = false;
        if(substr($item, 0, 1) == "-") $negate = true;
        // If we negate the item, only encode everything after the minus
        $item = $negate ? urlencode(substr($item, 1)) : urlencode($item);
        // Then add the minus back on after we encode it
        if($negate) $item = "-" . $item;
      }
      $string .= implode(" || ", $items) . "&";
    }
    return $string;
  }

  /*
   * This function parses our sorting and sort order options
   */
  private function gos_sort_order() {
    $string = "";
    if(isset($this->options["sort"]) && in_array($this->options["sort"], $this->sortOptions)) {
      $string .= "sort=" . $options["sort"] . "&";
    }
    if(isset($this->options["order"])) {
      $order = strtolower($this->options["order"]);
      if($order == "asc" || $order == "desc") {
        $string .= "direction=" . $order . "&";
      }
    }
    return $string;
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
   * Resets your options to the default (blank) state for the API call
   */
  public function resetOptions() {
    $this->options = array();
  }

  /*
   * Sets the options for a URL string. This is typically used for searches when
   * you're doing some API call related to items. All this function does is take
   * an associative array, which this class uses to build the API key url
   */
  public function setOptions($options) {
    return $this->options = array_merge($this->options, $options);
  }

  /*
   * Tests the API key entered in via the constructor. Note that this function
   * is never implicitly called. You should really only use this to test API
   * keys and get the User ID and Username associated with keys.
   */
  public function testConnection() {
    $response = $this->request("/keys/" . $this->api_key);
    if($response->key == $this->api_key) {
      return $response;
    }
    return false;
  }

  public function getUserGroups($user_id) {
    $response = $this->request(
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
    $response = $this->request(
      "/users/" .
      $user_id .
      "/collections"
    );
    return $this->parseCollections($response);
  }

  public function getGroupCollections($group_id) {
    return $this->request(
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
    $response = $this->request(
      "/users/" .
      $user_id .
      "/tags"
    );
    return $this->parseTags($response);
  }

  public function getGroupTags($group_id) {
    $response = $this->request(
      "/groups/" .
      $group_id .
      "/tags"
    );
    return $this->parseTags($response);
  }

  /*
  ** This function takes an items response from the Zotero API and parses it into
  ** something less verbose, making it easier to develop with.
  */
  private function parseItems($response) {
    if($response == false) return false;

    $items = array();
    if(is_array($response)) {
      foreach($response as $raw_item) {
        $items[] = $this->parseItem($raw_item);
      }
    } else {
      $items = $this->parseItem($response);
    }
    return $items;
  }

  /*
  ** This function takes an item from the Zotero API and parses it into
  ** something less verbose, making it easier to develop with.
  */

  private function parseItem($raw_item) {
    // Saves only the important elements of a raw item
    $item = new \StdClass();
    $item->key = $raw_item->key;
    $item->id = $raw_item->key;
    $item->url = $raw_item->links->alternate->href;
    $item->type = $raw_item->data->itemType;
    $item->title = $raw_item->data->title;
    $item->creators = $raw_item->data->creators;
    $item->abstract = $raw_item->data->abstractNote;
    $item->series = $raw_item->data->series;
    $item->seriesNumber = $raw_item->data->seriesNumber;
    $item->volume = $raw_item->data->volume;
    $item->numberOfVolumes = $raw_item->data->numberOfVolumes;
    $item->edition = $raw_item->data->edition;
    $item->place = $raw_item->data->place;
    $item->publisher = $raw_item->data->publisher;
    $item->date = $raw_item->data->date;
    $item->numPages = $raw_item->data->numPages;
    $item->language = $raw_item->data->language;
    $item->ISBN = $raw_item->data->ISBN;
    $item->shortTitle = $raw_item->data->shortTitle;
    // TODO: Tags
    // TODO: Collections
    return $item;
  }

  public function getUserItems($user_id) {
    $response = $this->request(
      "/users/" .
      $user_id .
      "/items"
    );
    return $this->parseItems($response);
  }

  public function getGroupItems($group_id) {

  }

  public function getCollectionItems($url) {
    $response = $this->request($url);
    return $this->parseItems($response);
  }

  public function getUserCollectionItems($user_id, $collection_key) {
    return $this->getCollectionItems(
      "/users/" . $user_id . "/collections/" . $collection_key . "/items"
    );
  }

  public function getGroupCollectionItems($group_id, $collection_key) {
    return $this->getCollectionItems(
      "/grups/" . $group_id . "/collections/" . $collection_key . "/items"
    );
  }

}
