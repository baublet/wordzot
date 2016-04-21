<?php

namespace Zotero;

interface Cache {
  public function set($key, $block);
  public function get($key);
  public function clear();
  public function destroy($key);
}
