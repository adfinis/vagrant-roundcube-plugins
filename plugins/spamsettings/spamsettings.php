<?php
/*
 +-------------------------------------------------------------------------+
 | Sasag Spam Settings Plugin for Roundcube                                |
 | @version 1.0                                                            |
 |                                                                         |
 | Copyright (C) 2013, Adfinis Sygroup.                                    |
 |                                                                         |
 +-------------------------------------------------------------------------+
 | This program lets the user change his amavis settings in the ldap       |
 | database from Roundcube settings tab                                    |
 +-------------------------------------------------------------------------+
 |                                                                         |
 | This program is free software; you can redistribute it and/or modify    |
 | it under the terms of the GNU General Public License version 2          |
 | as published by the Free Software Foundation.                           |
 |                                                                         |
 | This program is distributed in the hope that it will be useful,         |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
 | GNU General Public License for more details.                            |
 |                                                                         |
 | You should have received a copy of the GNU General Public License along |
 | with this program; if not, write to the Free Software Foundation, Inc., |
 | 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.             |
 |                                                                         |
 +-------------------------------------------------------------------------+
 | Author: Roland Kaeser <roland.kaeser@adfinis-sygroup.ch>                |
 +-------------------------------------------------------------------------+

 */

define('LDAP_OPT_DIAGNOSTIC_MESSAGE', 0x0032);


/**
 * Change Sasag Spam Settings in Roundcube
 *
 * Plugin that adds functionality to change the users spam settings in his/hers ldap record
 *
 * @author Roland Kaeser
 */
class spamsettings extends rcube_plugin {
	public $task    = 'settings';
	public $noframe = true;
	public $noajax  = true;
	private $submitHasBeenFailed = false;


	/**
	 * Official plugin init function
	 *
	 */
	function init()  {
		$rcmail = rcmail::get_instance();
		$this->load_config();
		$this->add_texts('localization/',true);

		// add Tab label
		//$rcmail->output->add_label('spamsettings');
		$this->register_action('plugin.spamsettings',
			array($this, 'spamsettingsInit'));

		$this->register_action('plugin.spamsettings-save',
			array($this, 'spamsettingsSave'));

		$this->include_script('spamsettings.js');
		$this->include_stylesheet("spamsettings.css");
	}


	/**
	 * Internal init function for the plugin
	 *
	 */
	function spamsettingsInit()  {
		$this->register_handler('plugin.body', array($this, 'spamsettingsForm'));
		$rcmail = rcmail::get_instance();
		$rcmail->output->set_pagetitle("Spam-Einstellungen ändern");
		$rcmail->output->send('plugin');
	}

	/**
	 * Draws the user form when opening the plugin or after save the settings
	 *
	 */
	function spamsettingsForm()  {
		$rcmail = rcmail::get_instance();
		$config = rcmail::get_instance()->config;
		$spamData = $this->loadUserData();

		$rcmail->output->set_env(
			'product_name',
			$rcmail->config->get('product_name')
		);

		$quarantaineMailAddress =
			$rcmail->config->get('spamsettings_ldap_quarantaine_mail_address');

		$table = new html_table(array(
			'cols' => 2,
			'class' => 'scroller spam-maintable'
		));

		$spamLevelTable = new html_table(array('cols' => 3));

		$whitelistTable = new html_table(array('cols' => 3));
		$whitelistAddRemoveTable = new html_table(array(
			'rows'        => 2,
			'cols'        => 1,
			'cellpadding' => 0,
			'cellspacing' => 0
		));

		$blacklistTable = new html_table(array('cols' => 3));

		$blacklistAddRemoveTable = new html_table(array(
			'rows'        => 2,
			'cols'        => 1,
			'cellpadding' => 0,
			'cellspacing' => 0
		));

		if ($spamData == false) {
			$rcmail->output->command(
				'display_message',
				"FEHLER: Konnte die Daten nicht aus dem LDAP-Server laden!",
				'confirmation'
			);
		}


		// -----------------------------------------------------------------------
		// -------- Formgeneration: Fields Start ---------------------------------
		// -----------------------------------------------------------------------

		// -----Checkbox to to ask if the mails subject should be tagged in case of spam --------------------------------------

		$inputAmavisSpamModifiesSubj = new html_checkbox(array(
			'name' => 'inputAmavisSpamModifiesSubj',
			'id' => 'inputAmavisSpamModifiesSubj',
			'value' => "TRUE"
		));

		$table->add(
			array('class' => 'spamsettings-leftColumn'),
			html::label($field_id, Q($this->gettext('amavisSpamModifiesSubj'))
		));

		$table->add(
			array('class' => 'spamsettings-rightColumn'),
			$inputAmavisSpamModifiesSubj->show($spamData["amavisSpamModifiesSubj"]
		));

		// Inputfield for the subject modifying string in case of spam
		$inputAmavisSpamSubjectTag2 = new html_inputfield(array(
			'name'         => 'inputAmavisSpamSubjectTag2',
			'id'           => 'inputAmavisSpamSubjectTag2',
			'size'         => 20,
			'autocomplete' => 'off',
			'value'        => $spamData["amavisSpamSubjectTag2"]
		));

		$table->add(
			array('class' => 'spamsettings-leftColumn'),
			html::label($field_id, Q($this->gettext('amavisSpamSubjectTag2'))
		));

		$table->add(
			array('class' => 'spamsettings-rightColumn'),
			$inputAmavisSpamSubjectTag2->show()
		);

		// Checkbox to say that quarantained
		// mails also should be delivered to mailbox
		$inputAmavisSpamQuarantineTo = new html_checkbox(array(
			'name' => 'inputAmavisSpamQuarantineTo',
			'id' => 'inputAmavisSpamQuarantineTo',
			'value' => $_SESSION['username'])
		);

		$table->add(
			array('class' => 'spamsettings-leftColumn'),
			html::label($field_id, Q($this->gettext('amavisSpamQuarantineTo')))
		);
		$table->add(
			array('class' => 'spamsettings-rightColumn'),
			$inputAmavisSpamQuarantineTo->show($spamData["amavisSpamQuarantineTo"])
		);

		// SPAM Names table
		$table->add(
			array('class' => 'spamsettings-leftColumn'),
			html::label($field_id, "&nbsp;")
		);
		$table->add(
			array('class' => 'spamsettings-rightColumn'),
			html::label($field_id, "&nbsp;")
		);


		$spamLevelNames = $config->get('spamlevelnames');
		$spamLevelTexts = $config->get('spamleveltexts');
		$spamTagLevels = $config->get('spamtaglevels');

		$selectedValue = "";
		for($i=0; $i < count($spamLevelNames); $i++) {
			if ($spamTagLevels[$i] == $spamData["amavisSpamTag2Level"]) {
				$selectedValue = $spamLevelNames[$i];
			}
		}

		for($i=0; $i < count($spamLevelNames); $i++) {
			$radioButton = new html_radiobutton(array(
				'name' => "spamLevel",
				'id' => "SPAMLEVEL" . $i,
				'value' => $spamLevelNames[$i],
				'class' => 'radio'
			));
			$spamLevelTable->add(
				array(
					'valign' => 'top',
					'class' => 'spamsettings-spamNames'
				),
				html::label("SPAMLEVEL" . $i, "<b>" . $spamLevelNames[$i] . "</b>")
			);

			error_log("TEST: " . $spamLevelNames[$i] . " ::::: SpamTaglevels: "
				. $spamTagLevels[$i] . " / " . "LDAP-Data: "
				. $spamData["amavisSpamTag2Level"]);

			$spamLevelTable->add(
				array('valign' => 'top'),
				$radioButton->show($selectedValue)
			);

			// $spamLevelTable->add(array('valign' => 'top'), $radioButton->show());
			$spamLevelTable->add(
				array(
					'valign' => 'top',
					'class' => 'spamsettings-spamTexts'
				),
				html::label("SPAMLEVEL" . $i, $spamLevelTexts[$i])
			);
		}

		$spamSettingsOut = "<fieldset class=\"fieldSet\"><legend>" .
			$this->gettext('spamsettings') . "</legend><br />";

		$spamSettingsOut = $spamSettingsOut . $spamLevelTable->show();
		$spamSettingsOut = $spamSettingsOut . "</fieldset>";

		$table->add(array('colspan' => 2), $spamSettingsOut);


		// ------Select-Box with add/remove button to add or remove blacklisted senders -------------------------

		$selectCurrentBlacklistSender = new html_select(array(
			'name' => 'selectCurrentBlacklistSender',
			'id' => 'selectCurrentBlacklistSender',
			'size' => 10
		));

		if ($spamData["amavisBlacklistSender"]) {
			$selectCurrentBlacklistSender->add(
				$spamData["amavisBlacklistSender"],
				$spamData["amavisBlacklistSender"]
			);
		}

		$blacklistTable->add(null,$selectCurrentBlacklistSender->show());

		$buttonAddBlacklistSender = html::p(
			array('class' => 'addRemoveButtonPTag'),
			$rcmail->output->button(
				array(
					'command' => 'plugin.commandAddBlacklistSender',
					'type'    => 'input',
					'id'      => 'buttonAddBlacklistSender',
					'class'   => 'spamsettings-button',
					'label'   => $this->gettext("addButton")
				)
			)
		);

		$blacklistAddRemoveTable->add(
			array('class' => 'addRemoveButtonTopTD'),
			$buttonAddBlacklistSender
		);

		$buttonRemoveBlacklistSender = html::p(
			array('class' => 'addRemoveButtonPTag'),
			$rcmail->output->button(array(
				'command' => 'plugin.commandRemoveBlacklistSender',
				'type'    => 'input',
				'id'      => 'buttonRemoveBlacklistSender',
				'class'   => 'spamsettings-button',
				'label'   => $this->gettext("removeButton")
			))
		);
		$blacklistAddRemoveTable->add(null, $buttonRemoveBlacklistSender);

		$blacklistTable->add(
			array(
				valign  => 'top',
				'class' => 'blacklistAddRemoveBar'
			),
			$blacklistAddRemoveTable->show()
		);

		$inputBlacklistSender = new html_inputfield(array(
			'name' => 'inputBlacklistSender',
			'id'   => 'inputBlacklistSender',
			'size' => 25
		));

		$blacklistTable->add(
			array(valign => 'top', 'class' => 'blacklistInputField'),
			$inputBlacklistSender->show()
		);

		$blackListOut = "<fieldset class=\"fieldSet\"><legend>" . $this->gettext('amavisBlacklistSender') . "</legend><br />";
		$blackListOut = $blackListOut . $blacklistTable->show();
		$blackListOut = $blackListOut . "</fieldset>";

		$table->add(array('colspan' => 2),$blackListOut);


		// Select-Box with add/remove button to add or
		// remove whitelisted senders
		$selectCurrentWhitelistSender = new html_select(array(
			'name' => 'selectCurrentWhitelistSender',
			'id'   => 'selectCurrentWhitelistSender',
			'size' => 10
		));

		if ($spamData["amavisWhitelistSender"]) {
			$selectCurrentWhitelistSender->add(
				$spamData["amavisWhitelistSender"],
				$spamData["amavisWhitelistSender"]
			);
		}

		$whitelistTable->add(null, $selectCurrentWhitelistSender->show());

		$buttonAddWhitelistSender = html::p(
			array('class' => 'addRemoveButtonPTag'),
			$rcmail->output->button(array(
				'command' => 'plugin.commandAddWhitelistSender',
				'type'    => 'input',
				'id'      => 'buttonAddWhitelistSender',
				'class'   => 'spamsettings-button',
				'label'   => $this->gettext("addButton")
			))
		);
		$whitelistAddRemoveTable->add(
			array('class' => 'addRemoveButtonTopTD'),
			$buttonAddWhitelistSender
		);

		$buttonRemoveWhitelistSender = html::p(
			array('class' => 'addRemoveButtonPTag'),
			$rcmail->output->button(array(
				'command' => 'plugin.commandRemoveWhitelistSender',
				'type'    => 'input',
				'id'      => 'buttonRemoveWhitelistSender',
				'class'   => 'spamsettings-button',
				'label'   => $this->gettext("removeButton")
			))
		);
		$whitelistAddRemoveTable->add(null,$buttonRemoveWhitelistSender);

		$whitelistTable->add(
			array(
				valign => 'top',
				'class' => 'whitelistAddRemoveBar'
			),
			$whitelistAddRemoveTable->show()
		);

		$inputWhitelistSender = new html_inputfield(array(
			'name' => 'inputWhitelistSender',
			'id'   => 'inputWhitelistSender',
			'size' => 25
		));
		$whitelistTable->add(
			array(
				valign  => 'top',
				'class' => 'whitelistInputField'
			),
			$inputWhitelistSender->show()
		);


		$whiteListOut = "<fieldset class=\"fieldSet\"><legend>" .
			$this->gettext('amavisWhitelistSender') . "</legend><br />";
		$whiteListOut = $whiteListOut . $whitelistTable->show();
		$whiteListOut = $whiteListOut . "</fieldset>";

		$table->add(array('colspan' => 2),$whiteListOut);

		// Save Button
		$saveButton =  html::p(
			null,
			$rcmail->output->button(
				array(
					'command' => 'plugin.spamsettings-save',
					'type'    => 'input',
					'class'   => 'button mainaction',
					'label'   => 'save'
				)
			)
		);

		$table->add(
			array('class' => 'spamsettings-leftColumn'),
			html::label(array('style' => 'font-size: 20px;'), "&nbsp;")
		);

		$table->add(
			array(
				'valign' => 'top',
				'style'  => 'height: 30px; text-align: right; padding-right: 10px;'
			),
			$saveButton
		);

		// Generate the table html output and sourround it with a div
		$out = html::div(
			array('id' => 'prefs-title', 'class' => 'boxtitle'),
			$this->gettext('spamsettingstitle')
		) . "<br />";

		$out = $out . $table->show() ;

		// Hidden Input-Fields to store the given new whitelist or
		// blacklist senders because the dynamicly added option value to a
		// select is not in the post
		$input_hiddenBlackListSenders = new html_hiddenfield(array(
			'name' => '_allBlackListSenders',
			'id'   => 'allBlackListSenders'
		));

		$input_hiddenWhiteListSenders = new html_hiddenfield(array(
			'name' => '_allWhiteListSenders',
			'id'   => 'allWhiteListSenders'
		));

		$out = $out . $input_hiddenBlackListSenders->show();
		$out = $out . $input_hiddenWhiteListSenders->show();

		// End of gui generation


		$rcmail->output->add_gui_object('spamsettingsform', 'spamsettings-form');
		$rcmail->output->add_script(
			"rcube_find_object('buttonAddBlacklistSender').value = "
			. "rcmail.gettext('addButton', 'spamsettings');"
		);

		// -----Generate the form tag around it and return the merged result

		return $rcmail->output->form_tag(array(
			'id'     => 'spamsettings-form',
			'name'   => 'spamsettings-form',
			'method' => 'post',
			'action' => './?_task=settings&_action=plugin.spamsettings-save',
		), $out);
	}


	/**
	 * Loads the current Users Spamsettings from the LDAP-Server, or, when a previous saveing attempt was failed, the last values from the post
	 *
	 *
	 * Returns: Array with the attributeName as key and its values as value
	 *          or false if something went wrong
	 */
	private function loadUserData() {
		$returnData = array();
		$config          = rcmail::get_instance()->config;
		$ldapHost        = $config->get('spamsettings_ldap_host',    'localhost');
		$baseDN          = $config->get('spamsettings_ldap_basedn',  'dc=company,dc=ch');
		$adminDN         = $config->get('spamsettings_ldap_adminDN', 'cn=config');
		$adminPW         = $config->get('spamsettings_ldap_adminPW', 'password');
		$currentUserName = $_SESSION['username'];
		$searchAttribute = $config->get('spamsettings_ldap_user_search_attribute', 'mail');
		$searchFilter    = "($searchAttribute=$currentUserName)";
		$fetchAttributes = array(
			"amavisSpamTag2Level",
			"amavisSpamKillLevel",
			"amavisSpamModifiesSubj",
			"amavisSpamSubjectTag2",
			"amavisSpamQuarantineTo",
			"amavisBlacklistSender",
			"amavisWhitelistSender"
		);

		if ($this->submitHasBeenFailed) {
			$currentBlackListSenders = explode(";",$fieldCurrentBlackListSenders);
			$currentWhiteListSenders = explode(";",$fieldCurrentWhiteListSenders);

			$returnData = array (
				"amavisSpamTag2Level" => $_POST['inputAmavisSpamTag2Level'],
				"amavisSpamKillLevel" => $_POST['_spamkilllevel'],
				"amavisSpamModifiesSubj" => $_POST['_spamtagenabled'],
				"amavisSpamSubjectTag2" => $_POST['_spamsubjecttag'],
				"amavisSpamQuarantineTo" => $_POST['_spamsendquarantainmails'],
				"amavisBlacklistSender" => $fieldCurrentBlackListSenders,
				"amavisWhitelistSender" => $fieldCurrentWhiteListSenders,
			);
			return $returnData;

		}

		$ldapConnection = ldap_connect($ldapHost, $config->get('spamsettings_ldap_port'));

		if (!$ldapConnection) {
			error_log("Spamsettings-Plugin: Error while connecting to the"
				. " LDAP-Server, the error was: "
				. ldap_error($ldapConnection));
			return false;
		}

		$ldapBind = ldap_bind($ldapConnection, $adminDN, $adminPW);

		if (!$ldapBind) {
			error_log("Spamsettings-Plugin: Error while binding to "
				. "the LDAP-Server, the error was: "
				. ldap_error($ldapConnection));
			return false;
		}

		$searchResult = ldap_search($ldapConnection, $baseDN, $searchFilter, $fetchAttributes);
		$resultData = ldap_get_entries($ldapConnection, $searchResult);

		if ($resultData["count"] == 0) {
			error_log("Spamsettings-Plugin: Error while retreiving data for: "
				. $_SESSION['username']
				. " The returned number of user records, based on the search filter: "
				. $searchFilter . " was 0: User not found!");
			return false;
		}

		foreach ($fetchAttributes as $attributeName) {
			if ($attributeName == 'amavisBlacklistSender' ||  $attributeName == 'amavisWhitelistSender') {
				$attributeData = array();
				$i = 0;
				foreach ($resultData[0][strtolower($attributeName)] as $attributeValue) {
					if ($i > 0) {
						$attributeData[$i] = $attributeValue;
					}
					$i++;
				}
				$returnData[$attributeName] = $attributeData;
			} else {
				$returnData[$attributeName] = $resultData[0][strtolower($attributeName)][0];
			}

		}
		ldap_close($ldapConnection);
		return $returnData;
	}


	/**
	 * Handels and revalidates the Post-Data from the Form to the LDAP Spamsettings in the user record
	 */
	function spamsettingsSave()  {
		$rcmail = rcmail::get_instance();

		$this->load_config();
		$this->add_texts('localization/');
		$this->register_handler('plugin.body', array($this, 'spamsettingsForm'));

		$config = rcmail::get_instance()->config;
		$rcmail->output->set_pagetitle($this->gettext('spamsettingstitle'));
		$quarantaineMailAddress = $rcmail->config->get('spamsettings_ldap_quarantaine_mail_address');
		$hasError = false;

		$spamLevelNames = $config->get('spamlevelnames');
		$spamTagLevels  = $config->get('spamtaglevels');
		$spamKillLevels = $config->get('spamkilllevels');

		if ($_POST['inputAmavisSpamModifiesSubj']) {
			$postAmavisSpamModifiesSubj = 1;
		} else {
			$postAmavisSpamModifiesSubj = 0;
		}

		$selectedID = -1;

		for ($i=0; $i < count($spamLevelNames); $i++) {
			if ($spamLevelNames[$i] == $_POST['spamLevel']) {
				$selectedID = $i;
			}
		}

		if ($selectedID < 0) {
			$postAmavisSpamTag2Level = "4";
			$postAmavisSpamKillLevel = "6";
		}
		else {
			$postAmavisSpamTag2Level = $spamTagLevels[$selectedID];
			$postAmavisSpamKillLevel = $spamKillLevels[$selectedID];
		}

		$postAmavisSpamSubjectTag2    = $_POST['inputAmavisSpamSubjectTag2'];
		$postAmavisSpamQuarantineTo   = $_POST['inputAmavisSpamQuarantineTo'];
		$fieldCurrentBlackListSenders = $_POST['_allBlackListSenders'];
		$fieldCurrentWhiteListSenders = $_POST['_allWhiteListSenders'];

		$currentBlackListSenders = explode(";",$fieldCurrentBlackListSenders);
		$currentWhiteListSenders = explode(";",$fieldCurrentWhiteListSenders);

		if (!$fieldSpamSendQuarantainedMails) {
			$postAmavisSpamQuarantineTo = $quarantaineMailAddress;
		}


		if ($postAmavisSpamModifiesSubj == 1 && $postAmavisSpamSubjectTag2 == '') {
			$rcmail->output->command(
				'display_message',
				$this->gettext('nospamsubjectTag'),
				'error'
			);

			$hasError = true;
		} elseif ($postAmavisSpamModifiesSubj == 1
				&& !preg_match('/[A-Za-z0-9\.#\-+@=\$]/i', $postAmavisSpamSubjectTag2) === 0) {
			$rcmail->output->command(
				'display_message',
				$this->gettext('wrongspamsubjectTag'),
				'error'
			);
			$hasError = true;
		}

		if (!$hasError) {
			$result = $this->saveUserData(
				$postAmavisSpamTag2Level,
				$postAmavisSpamKillLevel,
				$postAmavisSpamModifiesSubj,
				$postAmavisSpamSubjectTag2,
				$postAmavisSpamQuarantineTo,
				$currentBlackListSenders,
				$currentWhiteListSenders
			);

			if ($this->startsWith($result,"FAILED")) {
				$errorMessage = split(":", $result);
				$rcmail->output->command(
					'display_message',
					$this->gettext('updatefailed') . " "
						. $errorMessage[1]
						. $this->gettext('updatefailed2'),
						'error'
				);
			}
			else {
				$rcmail->output->command(
					'display_message',
					$this->gettext('updatesuccess'),
					'confirmation'
				);
			}
		}
		rcmail_overwrite_action('plugin.spamsettings');
		$rcmail->output->send('plugin');
	}



	/**
	 * Saves the given and checked Spamsettings to the user record in the LDAP-Server
	 *
	 * Parameter:	1: Numeric String (float): amavisSpamTag2Level: The Spam level from which the mail is spam tagged
	 *			2: Numeric String (float): amavisSpamKillLevel: The Spam level from which the mail is deleted directly
	 *			3: Boolean/Numeric: True(1) if the mails subject should be modified if the mail is tagged as spam
	 *			4: String: The mail address to which qurantained mails are sent. Normaly central mail account, configured in config value: "spamsettings_ldap_quarantaine_mail_address"
	 *			   If the user wants become all quarantaine mails, this mail address is set to his own
	 *			5: Array: The mail addresses which the user has blacklisted
	 *			6: Array. The mail addresses which the user has whitelisted
	 *
	 *
	 * Returns: 	String with save state:
	 *         		If the string begins with: "SUCCESS:" the saving process was successful
	 *          	if the string begins with  "FAILED:" the savigng process was unsuccessful and the following string (after :) is the error message for the user
	 */
	private function saveUserData($amavisSpamTag2Level,$amavisSpamKillLevel,$amavisSpamModifiesSubj,$amavisSpamSubjectTag2,$amavisSpamQuarantineTo,$amavisBlacklistSender,$amavisWhitelistSender) {
		$returnData = array();
		$config          = rcmail::get_instance()->config;
		$ldapHost        = $config->get('spamsettings_ldap_host', 'localhost');
		$baseDN          = $config->get('spamsettings_ldap_basedn', 'dc=company,dc=ch');
		$adminDN         = $config->get('spamsettings_ldap_adminDN', 'cn=config');
		$adminPW         = $config->get('spamsettings_ldap_adminPW', 'password');
		$currentUserName = $_SESSION['username'];
		$searchAttribute = $config->get('spamsettings_ldap_user_search_attribute', 'mail');
		$searchFilter    = "($searchAttribute=$currentUserName)";
		$fetchAttributes = array(
			"dn",
			"amavisSpamTag2Level",
			"amavisSpamKillLevel",
			"amavisSpamModifiesSubj",
			"amavisSpamSubjectTag2",
			"amavisSpamQuarantineTo",
			"amavisBlacklistSender",
			"amavisWhitelistSender"
		);

		$ldapConnection = ldap_connect($ldapHost, $config->get('spamsettings_ldap_port'));
		if (!$ldapConnection) {
			error_log("Spamsettings-Plugin: Error while connecting to the "
				. "LDAP-Server, the error was: " . ldap_error($ldapConnection));
			return "FAILED:" . $this->gettext('noldapconnection');
		}

		$ldapBind = ldap_bind($ldapConnection, $adminDN, $adminPW);

		if (!$ldapBind) {
			error_log("Spamsettings-Plugin: Error while binding to the"
				. " LDAP-Server, the error was: "
				. ldap_error($ldapConnection));
			return "FAILED:" . $this->gettext('noldapbind');
		}

		$searchResult = ldap_search(
			$ldapConnection,
			$baseDN,
			$searchFilter,
			$fetchAttributes
		);
		$resultData = ldap_get_entries($ldapConnection, $searchResult);

		if ($resultData["count"] == 0) {
			error_log("Spamsettings-Plugin: Error searching for the User: "
				. "$currentUserName; User not found in LDAP!");
			return "FAILED:" . $this->gettext('usernotfound');
		}

		$userDN = $resultData[0]["dn"];

		$updateEntry = array();
		$updateEntry['amavisSpamTag2Level'] = array($amavisSpamTag2Level);
		$updateEntry['amavisSpamKillLevel'] = array($amavisSpamKillLevel);
		if ($amavisSpamModifiesSubj == 1) {
			$updateEntry['amavisSpamModifiesSubj'] = 'TRUE';
		} else {
			$updateEntry['amavisSpamModifiesSubj'] = 'FALSE';
		}
		$updateEntry['amavisSpamQuarantineTo'] = array($amavisSpamQuarantineTo);
		$updateEntry['amavisSpamSubjectTag2'] = array($amavisSpamSubjectTag2);

		if (count($amavisBlacklistSender) > 0) {
			$updateEntry['amavisBlacklistSender'] = $amavisBlacklistSender;
		} else {
			$updateEntry['amavisBlacklistSender'] = array();
		}

		if (count($amavisWhitelistSender) > 0) {
			$updateEntry['amavisWhitelistSender'] = $amavisWhitelistSender;
		} else {
			$updateEntry['amavisWhitelistSender'] = array();
		}

		$updateResult = ldap_modify($ldapConnection, $userDN, $updateEntry);
		if ($updateResult) {
			return "SUCCESS:" . $this->gettext('updatesuccess');
		} else {
			return "FAILED:" . $this->gettext('updatefailed');
		}
	}


	/**
	 * Checks if the given string starts with the searchstring content
	 *
	 * Parameter:	1: String: The text to search in
	 *   		2: String: The searchstring within the first given argument
	 *
	 * Returns:		boolean; true if the given text starts with the search string
	 */
	private function startsWith($text, $searchString) {
		return !strncmp($text, $searchString, strlen($searchString));
	}



}
