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
$labels['spamsettings']  = 'Spam-Settings';
$labels['spamsettingstitle']  = 'Spam-Settings';
$labels['amavisSpamTag2Level']  = 'SPAM-Level from which the mail is tagged as spam:';
$labels['amavisSpamKillLevel']  = 'SPAM-Level from which the mail is deleted directly:';
$labels['amavisSpamModifiesSubj']  = 'Should the subject be modified when the mail is tagged as spam:';
$labels['amavisSpamSubjectTag2']  = 'Subject addon when mail is spam tagged:';
$labels['amavisSpamQuarantineTo']  = 'Also deliver virus mails:';
$labels['amavisBlacklistSender']  = 'Blocked senders:';
$labels['amavisWhitelistSender']  = 'Whitelisted senders';
$labels['addButton']  = 'Add sender address';
$labels['removeButton']  = 'Remove sender address';
$labels['noSender'] = 'Missing sender address for adding to the list';
$labels['noentryselected'] = 'No sender selected.';
$labels['novalidemail'] = 'The provided email address is not valid';
$labels['nospamlevel'] = 'The given spam tag level must be a number and between 1.0 and 14.0';
$labels['nokilllevel'] = 'the given spam kill level must be a number and between 1.0 and 16.0';
$labels['killlevelbelowtaglevel'] = 'The given spam kill level must be greater then the given spam tag tag level';
$labels['wrongspamsubjectTag'] = 'The spam addon contains invalid characters, allowed characters for this is only: a-z, 0-9, +, -, #, *';
$labels['nospamsubjectTag'] = 'If subject should be changed in case of spam tagged mail, the spam subject tag addon field must contain the tag text';

$messages = array();
$messages['noldapconnection'] = 'Unable to connect the database server';
$messages['noldapbind'] = 'The logon to the database server failed';
$messages['usernotfound'] = 'Could not find the user record in the database';
$messages['updatesuccess'] = 'Successful updated the settings in the database';
$messages['updatefailed'] = 'Could not update the settings in the database. The reason was: ';
$messages['updatefailed2'] = 'Please contact the support';

?>
