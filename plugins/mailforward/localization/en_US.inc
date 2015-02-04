<?php

/*
 +--------------------------------------------------------------------------+
 | plugins/mailforward/localization/<lang>.inc                              |
 |                                                                          |
 | Localization file of the Adfinis Sygroup Roundcube mailforwarding plugin |
 | Copyright (C) 2013, Adfinis Sygroup AG (Roland Käser)                    |
 |                                                                          |
 | Licensed under the GNU General Public License version 3 or               |
 | any later version with exceptions for skins & plugins.                   |
 |                                                                          |
 +--------------------------------------------------------------------------+
*/
$labels = array();
$labels['mailforwarding']  = 'Mailforwarding';
$labels['mailforwardtitle']  = 'Setup Mailforwarding';
$labels['enablemailforward']  = 'Mailforwarding enabled:';
$labels['mailforwardaddress']  = 'Forwardingaddress:';
$labels['keepcopy']  = 'Keep a local copy of all received emails:';

$labels['noSender'] = 'Please a valid email address';
$labels['novalidemail'] = 'Please provide a valid email';

$messages = array();
$messages['noldapconnection'] = 'Cold not connect to the database server';
$messages['noldapbind'] = 'Could not login to the database server';
$messages['usernotfound'] = 'Could not find user record in the database';
$messages['updatesuccess'] = 'Forwarding settings successfully updated';
$messages['updatefailed'] = 'Could not update the forwarding settings. The reason was: ';
$messages['updatefailed2'] = 'Please contact the support';

?>
