<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\Registry;

////
// Sets the status of a special product
  function tep_set_specials_status($specials_id, $status) {
    $KUUZU_Db = Registry::get('Db');

    return $KUUZU_Db->save('specials', ['status' => $status, 'date_status_change' => 'now()'], ['specials_id' => $specials_id]);
  }

////
// Auto expire products on special
  function tep_expire_specials() {
    $KUUZU_Db = Registry::get('Db');

    $Qspecials = $KUUZU_Db->query('select specials_id from :table_specials where status = 1 and now() >= expires_date and expires_date > 0');

    if ($Qspecials->fetch() !== false) {
      do {
        tep_set_specials_status($Qspecials->valueInt('specials_id'), 0);
      } while ($Qspecials->fetch());
    }
  }
?>
