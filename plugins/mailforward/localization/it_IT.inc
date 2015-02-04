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
$labels['mailforwarding']  = 'Mailweiterleitung';
$labels['mailforwardtitle']  = 'Mailweiterleitung einrichten';
$labels['enablemailforward']  = 'Mailweiterleritung aktiviert:';
$labels['mailforwardaddress']  = 'Weiterleitungsadresse:';
$labels['keepcopy']  = 'Kopie aller eingehenden Mails im Posteingang behalten:';

$labels['noSender'] = 'Bitte geben Sie eine Weiterleitungsadresse im rechten Eingabefeld ein';
$labels['novalidemail'] = 'Bitte geben Sie eine gültige E-Mail-Adresse an';

$messages = array();
$messages['noldapconnection'] = 'Der Datenbank-Server konnte nicht kontaktiert werden';
$messages['noldapbind'] = 'Die Anmeldung am Datenbank-Server ist fehlgeschlagen';
$messages['usernotfound'] = 'Der Benutzerdatensatz in der Datenbank konnte nicht gefunden werden';
$messages['updatesuccess'] = 'Die Daten wurden erfolgreich aktualisiert';
$messages['updatefailed'] = 'Die Daten konnten nicht aktualisiert werden. Der Grund war: ';
$messages['updatefailed2'] = 'Bitte wenden Sie Sich an den Suppport';

?>
