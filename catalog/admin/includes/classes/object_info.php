<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  class objectInfo {

// class constructor
    function objectInfo($object_array) {
      foreach ($object_array as $key => $value) {
        $this->$key = $value;
      }
    }
  }
?>
