<?php

/*
 +-----------------------------------------------------------------------+
 | plugins/spamsettings/localization/<lang>.inc                          |
 |                                                                       |
 | Localization file of the Adfinis Sygroup Roundcub Spamsettings plugin |
 | Copyright (C) 2013, Adfinis Sygroup AG (Roland Käser)                 |
 |                                                                       |
 | Licensed under the GNU General Public License version 3 or            |
 | any later version with exceptions for skins & plugins.                |
 |                                                                       |
 +-----------------------------------------------------------------------+
*/
$labels = array();
$labels['spamsettings']  = 'Spam-Einstellungen';
$labels['spamsettingstitle']  = 'Spam-Einstellungen ändern';
$labels['amavisSpamTag2Level']  = 'SPAM-Level ab welchem die Mail als SPAM gekennzeichnet wird:';
$labels['amavisSpamKillLevel']  = 'SPAM-Level ab welchem die Mail direkt gelöscht wird:';
$labels['amavisSpamModifiesSubj']  = 'Betreffzeile bei SPAM-Verdacht verändern:';
$labels['amavisSpamSubjectTag2']  = 'Betreff-Zusatz bei Spam-Verdacht:';
$labels['amavisSpamQuarantineTo']  = 'Virus-Mails auch zustellen:';
$labels['amavisBlacklistSender']  = 'Blockierte Absender';
$labels['amavisWhitelistSender']  = 'Vom Spamcheck ausgenommene Absender';
$labels['addButton']  = 'Absender hinzufügen';
$labels['removeButton']  = 'Absender löschen';
$labels['noSender'] = 'Bitte geben Sie einen Absender im rechten Eingabefeld ein';
$labels['noentryselected'] = 'Es wurde kein Eintrag ausgewählt.';
$labels['novalidemail'] = 'Bitte geben Sie eine gültige E-Mail-Adresse an';
$labels['nospamlevel'] = 'Der SPAM-Level ab welchem die Mail als SPAM gekennzeichnet wird, muss als Zahl (1.0 - 14.0) angegeben werden';
$labels['nokilllevel'] = 'Der SPAM-Level ab welchem die Mail direkt gelöscht wird, muss als Zahl (1.0 - 16.0) angegeben werden';
$labels['killlevelbelowtaglevel'] = 'Der SPAM-Level ab welchem die Mail direkt gelöscht wird, muss grösser sein als der SPAM-Level ab welchem die Mail als SPAM gekennzeichnet wird';
$labels['wrongspamsubjectTag'] = 'Der Betreff-Zusatz bei Spamverdacht enthält ungültige Zeichen. Erlaubt sind nur a-z, 0-9, +, -, #, *';
$labels['nospamsubjectTag'] = 'Wenn die Option "Betreffzeile bei SPAM-Verdacht verändern" aktiviert ist muss der Eintrag "Betreff-Zusatz bei Spam-Verdacht" ausgefüllt sein';

$messages = array();
$messages['noldapconnection'] = 'Der Datenbank-Server konnte nicht kontaktiert werden';
$messages['noldapbind'] = 'Die Anmeldung am Datenbank-Server ist fehlgeschlagen';
$messages['usernotfound'] = 'Der Benutzerdatensatz in der Datenbank konnte nicht gefunden werden';
$messages['updatesuccess'] = 'Die Daten wurden erfolgreich aktualisiert';
$messages['updatefailed'] = 'Die Daten konnten nicht aktualisiert werden. Der Grund war: ';
$messages['updatefailed2'] = 'Bitte wenden Sie Sich an den Suppport';

?>
