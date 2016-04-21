<?php
namespace Zotero;

require("Cache.php");

class fsCache implements Cache {

  private   $directory = false,
            $data = null,
            $options = null;

  private $default_options = array(
    "expiration" => 259200, /* Three days */
  );

  public function __construct($directory, $options = null) {
    return $this->init($directory, $options);
  }

  public function init($directory, $options = null) {
    if(is_writable($directory)) {
      $this->directory = $directory;
      $this->data = null;
      $this->options = ($options == null) ? $this->default_options : $options;
      return $this;
    }
    $this->directory = false;
    $this->data = null;
    $this->options = null;
    return false;
  }

  public function set($key, $block) {
    $this->destroy($key);
    $store = array(
      "key" => $key,
      "expiration" => (time() + $this->options["expiration"]),
      "value" => serialize($block)
    );
    $store_text = serialize($store);
    $filename = $this->filename($key);
    if(file_put_contents($filename, $store_text) > 0) {
      // Store the data on our object here so we don't have to load things from
      // the cache that we've saved to it on this request
      $this->data[$key] = $block;
      return true;
    }
    return false;
  }

  public function get($key) {
    // Don't bother to load something from the filesystem that we've already
    // saved on this request
    if(isset($this->data[$key])) return $this->data[$key];

    $filename = $this->filename($key);
    if(is_file($filename)) {
      $stored_text = file_get_contents($filename);
      $stored_data = unserialize($stored_text);
      if($stored_data !== false) {
        $expiration = $stored_data["expiration"];
        if(time() - $expiration < $this->options["expiration"]) {
          $value = unserialize($stored_data["value"]);
          if($value !== false) {
            return $value;
          }
        }
      }
    }
    return false;
  }

  public function clear() {
    $files = glob($this->directory . "/*"); // get all file names
    foreach($files as $filename) {
      if(is_file($filename)) unlink($filename);
    }
  }

  public function destroy($key) {
    $filename = $this->filename($key);
    if(is_file($filename)) {
      return unlink($filename);
    }
    return false;
  }

  private function filename($key) {
    return $this->directory . "/" . md5($key) . ".zotcache";
  }
}
