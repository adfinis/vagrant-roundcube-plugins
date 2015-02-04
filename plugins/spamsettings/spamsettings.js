/**
 * Spam Settings plugin script
 * @version 1.0
 */

if (window.rcmail) {
  rcmail.addEventListener('init', function(evt) {
    var tab = $('<span>').attr('id', 'settingstabpluginspamsettings').addClass('tablink spamsettings');
    var button = $('<a>').attr('href', rcmail.env.comm_path+'&_action=plugin.spamsettings').html(rcmail.gettext('spamsettings', 'spamsettings')).appendTo(tab);

    // add button and register commands
    rcmail.add_element(tab, 'tabs');

    rcmail.register_command('plugin.spamsettings-save', function() { rcmail.spamsettingsCommandSave() },true);
    rcmail.register_command('plugin.commandAddBlacklistSender',    function() { rcmail.spamsettingsCommandAddBlacklistSender() },true);
    rcmail.register_command('plugin.commandRemoveBlacklistSender', function() { rcmail.spamsettingsCommandRemoveBlacklistSender() },true);
    
    rcmail.register_command('plugin.commandAddWhitelistSender',    function() { rcmail.spamsettingsCommandAddWhitelistSender() },true);
    rcmail.register_command('plugin.commandRemoveWhitelistSender', function() { rcmail.spamsettingsCommandRemoveWhitelistSender() },true);

    if (rcmail.env.action == 'plugin.spamsettings'  || rcmail.env.action == 'plugin.spamsettings-save') {
    	var buttonAddBlacklistSenderRef = rcube_find_object('buttonAddBlacklistSender');
	    buttonAddBlacklistSenderRef.value = rcmail.gettext('addButton','spamsettings');
	    buttonAddBlacklistSenderRef.disabled = false;
	
	var buttonRemoveBlacklistSenderRef = rcube_find_object('buttonRemoveBlacklistSender');
	    buttonRemoveBlacklistSenderRef.value = rcmail.gettext('removeButton','spamsettings');
	    buttonRemoveBlacklistSenderRef.disabled = false;
    
	var buttonAddWhitelistSenderRef = rcube_find_object('buttonAddWhitelistSender');
	    buttonAddWhitelistSenderRef.value = rcmail.gettext('addButton','spamsettings');
	    buttonAddWhitelistSenderRef.disabled = false;
    
	var buttonRemoveWhitelistSenderRef = rcube_find_object('buttonRemoveWhitelistSender');
	    buttonRemoveWhitelistSenderRef.value = rcmail.gettext('removeButton','spamsettings');
    
	var selectCurrentBlacklistSenderRef = rcube_find_object('selectCurrentBlacklistSender');
	    selectCurrentBlacklistSenderRef.disabled = false;
	
	var selectCurrentWhitelistSenderRef = rcube_find_object('selectCurrentWhitelistSender');
	    selectCurrentWhitelistSenderRef.disabled = false;
    }
  });
 
}


rcube_webmail.prototype.spamsettingsCommandAddBlacklistSender = function() {
	var inputBlacklistSenderRef = rcube_find_object('inputBlacklistSender');
    
	if (inputBlacklistSenderRef.value == '') {
	    alert(rcmail.gettext('noSender', 'spamsettings'));
	    inputBlacklistSenderRef.focus();
	    return false;
	}
    
	if (inputBlacklistSenderRef.value.match('[^.a-zA-Z\-0-9\@\#\=]')) {
	    alert(rcmail.gettext('novalidemail', 'spamsettings'));
    	    inputBlacklistSenderRef.focus();
    	    return false;    
	}
    
	if ( parent.rcmail.spamsettingsValidateEMail(inputBlacklistSenderRef.value) != true) {
	    alert(rcmail.gettext('novalidemail', 'spamsettings'));
    	    inputBlacklistSender.focus();
    	    return false;
	}
	var opt = document.createElement('option');
        opt.value = inputBlacklistSenderRef.value;
	opt.innerHTML = inputBlacklistSenderRef.value;
     
	document.forms["spamsettings-form"].selectCurrentBlacklistSender.add(opt);
	inputBlacklistSenderRef.value = '';
	parent.rcmail.collectAllBlackListSenders();
}



rcube_webmail.prototype.spamsettingsCommandRemoveBlacklistSender = function() {

	var inputBlacklistSenderRef = rcube_find_object('inputBlacklistSender');
	var selectCurrentBlacklistSenderRef = rcube_find_object('selectCurrentBlacklistSender');
    
	if (selectCurrentBlacklistSenderRef.selectedIndex < 0) {
	     alert(rcmail.gettext('noentryselected', 'spamsettings'));
	} else {
    	     var selectedEMailAddress = selectCurrentBlacklistSenderRef.options[selectCurrentBlacklistSenderRef.selectedIndex].text;
             inputBlacklistSenderRef.value = selectedEMailAddress;
             selectCurrentBlacklistSenderRef.remove(selectCurrentBlacklistSenderRef.selectedIndex);
             parent.rcmail.collectAllBlackListSenders();
	}
}


rcube_webmail.prototype.spamsettingsCommandAddWhitelistSender = function() {
	var inputWhitelistSenderRef = rcube_find_object('inputWhitelistSender');
	var selectCurrentWhitelistSenderRef = rcube_find_object('selectCurrentWhitelistSender');
    
        if (inputWhitelistSenderRef.value == '') {
	    alert(rcmail.gettext('noSender', 'spamsettings'));
	    return false;
	    input_whitelistSenderInputField .focus();
        }
    
	if (inputWhitelistSenderRef.value.match('[^.a-zA-Z\-0-9\@\#\=]')) {
	    alert(rcmail.gettext('novalidemail', 'spamsettings'));
    	    inputWhitelistSenderRef.focus();
    	    return false;    
	}

    
	if ( parent.rcmail.spamsettingsValidateEMail(inputWhitelistSenderRef.value) != true) {
    	    alert(rcmail.gettext('novalidemail', 'spamsettings'));
	    inputWhitelistSenderRef.focus();
    	    return false;
	}
    
	var opt = document.createElement('option');
	opt.value = inputWhitelistSenderRef.value;
	opt.innerHTML = inputWhitelistSenderRef.value;
	selectCurrentWhitelistSenderRef.appendChild(opt); 
     
	inputWhitelistSenderRef.value = '';
	parent.rcmail.collectAllWhiteListSenders();
}



rcube_webmail.prototype.spamsettingsCommandRemoveWhitelistSender = function() {
	    var inputWhitelistSenderRef = rcube_find_object('inputWhitelistSender');
	    var selectCurrentWhitelistSenderRef = rcube_find_object('selectCurrentWhitelistSender');
    
	    if (selectCurrentWhitelistSenderRef.selectedIndex <0) {
		 alert(rcmail.gettext('noentryselected', 'spamsettings'));
	    } else {
    		var selectedEMailAddress = selectCurrentWhitelistSenderRef.options[selectCurrentWhitelistSenderRef.selectedIndex].text;
    	        inputWhitelistSenderRef.value = selectedEMailAddress;
		    selectCurrentWhitelistSenderRef.remove(selectCurrentWhitelistSenderRef.selectedIndex);
    		parent.rcmail.collectAllWhiteListSenders();
	    }

}



rcube_webmail.prototype.spamsettingsCommandSave = function() {
	var inputAmavisSpamTag2LevelRef = rcube_find_object('inputAmavisSpamTag2Level');
	var inputAmavisSpamKillLevelRef = rcube_find_object('inputAmavisSpamKillLevel');
	var inputAmavisSpamSubjectTag2Ref = rcube_find_object('inputAmavisSpamSubjectTag2');
	var inputAmavisSpamModifiesSubjRef = rcube_find_object('inputAmavisSpamModifiesSubj');
	
	if ( inputAmavisSpamSubjectTag2Ref.value != '' && inputAmavisSpamSubjectTag2Ref.value.match('[^().a-zA-Z\#\* +\-0-9]') ) {
	    alert(rcmail.gettext('wrongspamsubjectTag', 'spamsettings'));
	    inputAmavisSpamSubjectTag2Ref.focus();
	    return;
	}
	
	parent.rcmail.collectAllBlackListSenders();
	parent.rcmail.collectAllWhiteListSenders();
	rcmail.gui_objects.spamsettingsform.submit();
}








rcube_webmail.prototype.spamsettingsValidateEMail = function(email) {
    var atpos=email.indexOf("@");
    var dotpos=email.lastIndexOf(".");
    if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length) {
        return false;
    }
    return true;
}


rcube_webmail.prototype.collectAllBlackListSenders = function() {
    var selectCurrentBlacklistSenderRef = rcube_find_object('selectCurrentBlacklistSender');
    var input_allBlackListSenders = rcube_find_object('_allBlackListSenders');
    
    
    input_allBlackListSenders.value = "";
    for (var i = 0; i < selectCurrentBlacklistSenderRef.options.length; i++) { 
	input_allBlackListSenders.value = input_allBlackListSenders.value + selectCurrentBlacklistSenderRef.options[i].value;
	if (i < (selectCurrentBlacklistSenderRef.options.length-1)) {
	    input_allBlackListSenders.value = input_allBlackListSenders.value + ";"
	}
    }
}



rcube_webmail.prototype.collectAllWhiteListSenders = function() {
    var selectCurrentWhitelistSenderRef = rcube_find_object('selectCurrentWhitelistSender');
    var input_allWhiteListSenders = rcube_find_object('_allWhiteListSenders');
    
    
    input_allWhiteListSenders.value = "";
    for (var i = 0; i < selectCurrentWhitelistSenderRef.options.length; i++) { 
	input_allWhiteListSenders.value = input_allWhiteListSenders.value + selectCurrentWhitelistSenderRef.options[i].value;
	if (i < (selectCurrentWhitelistSenderRef.options.length-1)) {
	    input_allWhiteListSenders.value = input_allWhiteListSenders.value + ";"
	}
    }
}


rcube_webmail.prototype.spamsettingsIsNumber = function(n) {
     return !isNaN(parseFloat(n)) && isFinite(n);
}










