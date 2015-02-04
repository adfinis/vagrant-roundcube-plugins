/**
 * Mail Forward plugin script
 * @version 1.0
 */
if (window.rcmail) {
    rcmail.addEventListener('init', function(evt) {
        // <span id="settingstabdefault" class="tablink"><roundcube:button command="preferences" type="link" label="preferences" title="editpreferences" /></span>
	var tab = $('<span>').attr('id', 'settingstabpluginmailforward').addClass('tablink mailforward');
	var button = $('<a>').attr('href', rcmail.env.comm_path+'&_action=plugin.mailforward').html(rcmail.gettext('mailforwarding', 'mailforward')).appendTo(tab);

	// add button and register commands
        rcmail.add_element(tab, 'tabs');
        rcmail.add_element(tab, 'tabs');

	rcmail.register_command('plugin.mailforward-save', function() { 
	    var inputForwardAddressRef = rcube_find_object('inputMailForwardAddress');

	    if ( inputForwardAddressRef.value != '' && parent.rcmail.mailforwardValidateEMail(inputForwardAddressRef.value) != true) {
		alert(rcmail.gettext('novalidemail', 'mailforward'));
    		input_forwardaddress.focus();
    		return false;
    	    }
    	    rcmail.gui_objects.mailforwardform.submit();
	}, true);
    },true);
}


rcube_webmail.prototype.checkboxOnChange = function() {
    var inputMailForwardEnabledRef = rcube_find_object('inputMailForwardEnabled');
    var inputForwardAddressRef = rcube_find_object('inputMailForwardAddress');
    var checkKeepLocalCopyRef = rcube_find_object('checkKeepLocalCopy');

    inputForwardAddressRef.disabled = !inputMailForwardEnabledRef.checked;
    checkKeepLocalCopy.disabled = !inputMailForwardEnabledRef.checked;
}

rcube_webmail.prototype.mailforwardValidateEMail = function(email) {
    var atpos=email.indexOf("@");
    var dotpos=email.lastIndexOf(".");
    if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length) {
        return false;
    }
    return true;
}
