<?php
/*
 +-------------------------------------------------------------------------+
 | Sasag Mail forward Plugin for Roundcube                            |
 | @version 1.0                                                            |
 |                                                                         |
 | Copyright (C) 2013, Adfinis Sygroup.                                    |
 |                                                                         |
 +-------------------------------------------------------------------------+
 | This program lets the user change his mail forwarding settings          |
 | in the ldap database from Roundcube settings tab                        |
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


/**
 * Change Sasag Mail forward Settings in Roundcube
 *
 * Plugin that adds functionality to change the users mail forward settings in his/hers ldap record
 *
 * @author Roland Kaeser
 */
class mailforward extends rcube_plugin {
	public $task                 = 'settings';
	public $noframe              = true;
	public $noajax               = true;
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
		//$rcmail->output->add_label('mailforward');
		$this->register_action('plugin.mailforward',      array($this, 'mailforwardInit'));
		$this->register_action('plugin.mailforward-save', array($this, 'mailforwardSave'));

		$this->include_script('mailforward.js');
		$this->include_stylesheet("mailforward.css");
	}

	/**
	 * Internal init function for the plugin
	 *
	 */
	function mailforwardInit()  {
		$this->register_handler('plugin.body', array($this, 'mailforwardForm'));
		$rcmail = rcmail::get_instance();
		$rcmail->output->set_pagetitle($this->gettext('mailforwardtitle'));
		$rcmail->output->send('plugin');
	}

	/**
	 * Draws the user form when opening the plugin or after save the settings
	 *
	 */
	function mailforwardForm()  {
		$rcmail = rcmail::get_instance();
		$forwardSettings = $this->loadUserData();

		$rcmail->output->set_env(
			'product_name',
			$rcmail->config->get('product_name')
		);
		$table = new html_table(
			array('cols' => 2, 'class' => 'scroller mailforward-maintable')
		);

		if ($forwardSettings == false) {
			//$rcmail->output->command('display_message', $this->gettext('successfullysaved'), 'confirmation');
			$rcmail->output->command(
				'display_message',
				'ERROR: Could not load data from LDAP-Server!',
				'confirmation'
			);
		}

		// ---------------------------------------------------------------------
		// -------- Formgeneration: Fields Start -------------------------------
		// ---------------------------------------------------------------------

		// -----Input-Field for enabling/disabling mail forwarding address  ---

		$enableMailForward = 'FALSE';

		if ($forwardSettings["mailForwardAddress"]) {
			$enableMailForward = 'TRUE';
		}

		$inputMailForwardEnabled = new html_checkbox(array(
			'name'     => 'inputMailForwardEnabled',
			'id'       => 'inputMailForwardEnabled',
			'value'    => "TRUE",
			'onchange' => 'parent.rcmail.checkboxOnChange();'
		));

		$table->add(
			array('class' => 'mailforward-leftColumn'),
			html::label(
				'enablemailforward',
				Q($this->gettext('enablemailforward')
			))
		);

		$table->add(
			array('class' => 'mailforward-rightColumn'),
			$inputMailForwardEnabled->show( $enableMailForward )
		);

		// -----Input-Field for setting the forward address  -----------------
		$mailForwardAddress     = "";
		$forwardAddressDisabled = false;

		if ($forwardSettings["mailForwardAddress"]) {
			$mailForwardAddress = $forwardSettings["mailForwardAddress"];
		}
		else if ($forwardSettings["mailForwardConfiguredAddress"]) {
			$mailForwardAddress = $forwardSettings["mailForwardConfiguredAddress"];
			$forwardAddressDisabled = true;
		}
		else {
			$forwardAddressDisabled = true;
		}

		$inputMailForwardAddress = new html_inputfield(array(
				'name'         => 'inputMailForwardAddress',
				'id'           => 'inputMailForwardAddress',
				'size'         => 40,
				'autocomplete' => 'off',
				'value'        => $mailForwardAddress,
				'disabled'     => $forwardAddressDisabled
		));

		$table->add(array(
			'class' => 'mailforward-leftColumn'),
			html::label('forwardaddress',
			Q($this->gettext('mailforwardaddress'))
		));

		$table->add(
			array('class' => 'mailforward-rightColumn'),
			$inputMailForwardAddress->show()
		);

		// -----Checkbox to to ask if the mail should be forwarded  ----------
		$mailForwardKeepLocalCopy = 'FALSE';

		if ($forwardSettings["mailForwardKeepLocalCopy"]
			&& $forwardSettings["mailForwardKeepLocalCopy"] == 'TRUE') {
			$mailForwardKeepLocalCopy = 'TRUE';
		}

		$checkKeepLocalCopy = new html_checkbox(array(
			'name'     => 'checkKeepLocalCopy',
			'id'       => 'checkKeepLocalCopy',
			'value'    => "TRUE",
			'disabled' => $forwardAddressDisabled
		));

		$table->add(
			array('class' => 'mailforward-leftColumn'),
			html::label('keeplocalcopy', Q($this->gettext('keepcopy')))
		);
		$table->add(
			array('class' => 'mailforward-rightColumn'),
			$checkKeepLocalCopy->show($mailForwardKeepLocalCopy
		));

		// -----Save Button --------------------------------------
		$saveButton = html::p(
			null,
			$rcmail->output->button(
				array(
					'command' => 'plugin.mailforward-save',
					'type'    => 'input',
					'class'   => 'button mainaction',
					'label'   => 'save'
				)
			)
		);

		$table->add(
			array(
				'class' => 'mailforward-leftColumn'),
				html::label( array ('style' => 'font-size: 20px;'),
				"&nbsp;"
			)
		);

		$table->add(
			array(
				'valign' => 'top',
				'style' => 'height: 30px; text-align: left; padding-right: 10px;'
			),
			$saveButton
		);

		// -----Generate the table html output and sourround it with a div -----
		$out = html::div(
			array(
				'id' => 'prefs-title',
				'class' => 'boxtitle'
			),
			$this->gettext('mailforwardtitle')) . "<br />";
		$out = $out . $table->show() ;

		// ------End of gui generation ---------------------------------------
		$rcmail->output->add_gui_object('mailforwardform', 'mailforward-form');

		// -----Generate the form tag around it and return the merged result
		return $rcmail->output->form_tag(
			array(
				'id'     => 'mailforward-form',
				'name'   => 'mailforward-form',
				'method' => 'post',
				'action' => './?_task=settings&_action=plugin.mailforward-save',
			),
			$out
		);
	}

	/**
	 * Loads the current Users mailforward from the LDAP-Server, or, when a
	 * previous saveing attempt was failed, the last values from the post
	 *
	 *
	 * Returns: Array with the attributeName as key and its values as value
	 *          or false if something went wrong
	 */
	private function loadUserData() {
		$returnData      = array();
		$config          = rcmail::get_instance()->config;
		$ldapHost        = $config->get('mailforward_ldap_host', 'localhost');
		$baseDN          = $config->get('mailforward_ldap_basedn', 'dc=company,dc=ch');
		$adminDN         = $config->get('mailforward_ldap_adminDN', 'cn=config');
		$adminPW         = $config->get('mailforward_ldap_adminPW', 'password');
		$currentUserName = $_SESSION['username'];
		$searchAttribute = $config->get('mailforward_ldap_user_search_attribute', 'mail');
		$searchFilter    = "($searchAttribute=$currentUserName)";
		$fetchAttributes = array(
			"mailForwardConfiguredAddress",
			"mailForwardAddress",
			"mailForwardKeepLocalCopy"
		);

		if ($this->submitHasBeenFailed) {
			$returnData["mailForwardKeepLocalCopy"] = $_POST['checkKeepLocalCopy'];
			$returnData["mailForwardAddress"]       = $_POST['inputMailForwardAddress'];
			return $returnData;
		}

		$ldapConnection = ldap_connect(
			$ldapHost,
			$config->get('mailforward_ldap_port')
		);

		if (!$ldapConnection) {
			error_log("Mailforward-Plugin: Error while connecting to"
				. " the LDAP-Server, the error was: "
				. ldap_error($ldapConnection));
			return false;
		}

		$ldapBind = ldap_bind($ldapConnection, $adminDN, $adminPW);

		if (!$ldapBind) {
			error_log("Mailforward-Plugin: Error while binding"
				. " to the LDAP-Server, the error was: "
				. ldap_error($ldapConnection));
			return false;
		}

		$searchResult = ldap_search(
			$ldapConnection,
			$baseDN,
			$searchFilter,
			$fetchAttributes
		);

		$resultData = ldap_get_entries(
			$ldapConnection,
			$searchResult
		);

		if ($resultData["count"] > 0) {
			foreach ($fetchAttributes as $attributeName) {
				$returnData[$attributeName] =
					$resultData[0][strtolower($attributeName)][0];
			}
			ldap_close($ldapConnection);
			return $returnData;
		}
		else {
			error_log("Mailforward-Plugin: Error while retreiving"
				. " data for: " . $_SESSION['username']
				. " The returned number of user records, based"
				. " on the search filter: " . $searchFilter
				. " was 0: User not found!");
			return false;
		}
	}

	/**
	 * Handels and revalidates the Post-Data from the Form to the LDAP mailforward in the user record
	 */
	function mailforwardSave()  {
		$rcmail = rcmail::get_instance();

		$this->load_config();
		$this->add_texts('localization/');
		$this->register_handler('plugin.body', array($this, 'mailforwardForm'));
		$rcmail->output->set_pagetitle($this->gettext('mailforwardtitle'));

		$postKeepLocalCopy = 0;
		$enableForwarding  = false;

		$postMailForwardAddress = ltrim($_POST['inputMailForwardAddress']);
		$postMailForwardAddress = rtrim($postMailForwardAddress);

		if (!empty($postMailForwardAddress) &&
				!filter_var($postMailForwardAddress, FILTER_VALIDATE_EMAIL)) {
			$rcmail->output->command(
				'display_message',
				$this->gettext('novalidemail'),
				'error');

			$this->submitHasBeenFailed = true;
		}

		if ($_POST['inputMailForwardEnabled']) {
			$enableForwarding = true;
		}

		if ($_POST['checkKeepLocalCopy']) {
			$postKeepLocalCopy = 1;
		}

		if (empty($postMailForwardAddress) || !$_POST['inputMailForwardEnabled']) {
			$postKeepLocalCopy = 0;
		}

		if ($this->submitHasBeenFailed != true) {
			$result = $this->saveUserData(
				$enableForwarding,
				$postMailForwardAddress,
				$postKeepLocalCopy
			);

			if ($this->startsWith($result,"FAILED")) {
				$errorMessage = split(":", $result);
				$rcmail->output->command(
					'display_message',
					$this->gettext('updatefailed') . " " . $errorMessage[1]
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
		rcmail_overwrite_action('plugin.mailforward');
		$rcmail->output->send('plugin');
	}

	/**
	 * Saves the given and checked mailforward settings to the user-record in the LDAP-Server
	 *
	 * Parameter: 1: Boolean should forwarding be enabled (true) or no (false)
	 *            2: String: The mail forward address
	 *            3: Boolean: True if the server should keep a local copy (ignored if parameter 1 is false)
	 *
	 *
	 * Returns: String with save state:
	 *          If the string begins with: "SUCCESS:" the saving process was successful
	 *          if the string begins with  "FAILED:" the savigng process was unsuccessful and the following string (after :) is the error message for the user
	 */
	private function saveUserData(
		$enableMailForwarding,
		$mailForwardAddress,
		$keepLocalCopy
	) {
		$returnData      = array();
		$config          = rcmail::get_instance()->config;
		$ldapHost        = $config->get('mailforward_ldap_host', 'localhost');

		$baseDN  = $config->get(
			'mailforward_ldap_basedn',
			'dc=company,dc=ch'
		);
		$adminDN = $config->get(
			'mailforward_ldap_adminDN',
			'cn=config'
		);

		$adminPW = $config->get(
			'mailforward_ldap_adminPW',
			'password'
		);

		$currentUserName = $_SESSION['username'];
		$searchAttribute = $config->get(
			'mailforward_ldap_user_search_attribute',
			'mail'
		);

		$searchFilter    = "($searchAttribute= $currentUserName)";
		$fetchAttributes = array(
			"dn",
			"mailForwardAddress",
			"mailForwardKeepLocalCopy"
		);

		$ldapConnection = ldap_connect(
			$ldapHost,
			$config->get('mailforward_ldap_port')
		);

		if (!$ldapConnection) {
			error_log("mailforward-Plugin: Error while connecting to the"
				. " LDAP-Server, the error was: " . ldap_error($ldapConnection)
			);
			return "FAILED:" . $this->gettext('noldapconnection');
		}

		$ldapBind = ldap_bind($ldapConnection, $adminDN, $adminPW);

		if (!$ldapBind) {
			error_log("mailforward-Plugin: Error while binding to the"
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
			error_log("mailforward-Plugin: Error searching for"
				. " the User: $currentUserName; User not found in LDAP!"
			);
			return "FAILED:" . $this->gettext('usernotfound');
		}

		$userDN = $resultData[0]["dn"];

		$updateEntry = array();

		if ($enableMailForwarding == true) {
			$updateEntry['mailForwardAddress']           = array($mailForwardAddress);
			$updateEntry['mailForwardConfiguredAddress'] = array($mailForwardAddress);
		}
		else {
			$updateEntry['mailForwardAddress'] = array();
		}

		if ($keepLocalCopy == 1) {
			$updateEntry['mailForwardKeepLocalCopy'] = 'TRUE';
		}
		else {
			$updateEntry['mailForwardKeepLocalCopy'] = 'FALSE';
		}

		$updateResult = ldap_modify($ldapConnection, $userDN, $updateEntry);
		if ($updateResult) {
			return "SUCCESS:" . $this->gettext('updatesuccess');
		}
		else {
			return "FAILED:" . $this->gettext('updatefailed');
		}
	}

	/**
	 * Checks if the given string starts with the searchstring content
	 *
	 * Parameter:   1: String: The text to search in
	 *              2: String: The searchstring within the first given argument
	 *
	 * Returns:     boolean; true if the given text starts with the search string
	 */
	private function startsWith($text, $searchString) {
		return !strncmp($text, $searchString, strlen($searchString));
	}
}
