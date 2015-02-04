<?php

$rcmail_config['spamsettings_ldap_host'] = 'localhost';

// LDAP server port to connect to
// Default: '389'
$rcmail_config['spamsettings_ldap_port'] = '389';

// LDAP version
// Default: '3'
$rcmail_config['spamsettings_ldap_version'] = '3';

// LDAP base name (root directory)
// Exemple: 'dc=exemple,dc=com'
$rcmail_config['spamsettings_ldap_basedn'] = 'dc=company,dc=ch';

// LDAP Admin DN
// Default: value
$rcmail_config['spamsettings_ldap_adminDN'] = 'cn=admin,dc=company,dc=ch';

// LDAP Admin Password
// Default: value
$rcmail_config['spamsettings_ldap_adminPW'] = '';

$rcmail_config['spamsettings_ldap_user_search_attribute'] = 'mail';

// Default Quantaine-Mailaddress
$rcmail_config['spamsettings_ldap_quarantaine_mail_address'] = '';

// Define the names of the possible spam level settings
$rcmail_config['spamlevelnames'] = array("Aus","Niedrig","Normal", "Moderat","Hoch","Sehr Hoch");

// Define the amavisSpamTag2Level Settings for the above names
$rcmail_config['spamtaglevels'] = array(100,10,7,5,4,2);

// Define the amavisSpamKillLevel for the above names
$rcmail_config['spamkilllevels'] = array(200,200,200,200,6,4);

// Define the description texts for the above spam level names
$rcmail_config['spamleveltexts'] = array (
			    "Der Spam-Filter ist nicht aktiv. Es werden keine Emails gelöscht oder markiert.",
			    "Der Spam-Filter ist relativ schwach eingestellt. Einige Werbe-Emails werden nicht als solche identifiziert. Es ist sehr unwahrscheinlich dass eine normale Email als Werbemail identifiziert wird. Werbe-Emails werden nicht gelöscht, es wird lediglich die Betreffzeile markiert",
			    "Der Spam-Filter ist etwas stärker eingestellt. Viele Werbe-Emails werden als solche identifiziert. Es kann vorkommen, dass eine normale Email als Werbemail identifiziert wird. Werbe-Emails werden nicht gelöscht, es wird lediglich die Betreffzeile markiert. ",
			    "Der Spam-Filter ist stark eingestellt. Praktisch alle Werbe-Emails werden erkannt. Es kann vorkommen, dass eine normale Email als Werbemail identifiziert wird. Werbe-Emails werden nicht gelöscht, es wird lediglich die Betreffzeile markiert.",
			    "Der Spam-Filter ist etwas stärker eingestellt. Fast alle Werbe-Emails werden als solche identifiziert. Es kann vorkommen, dass eine normale Email als Werbemail identifiziert wird. Erkannte Werbe-Emails werden gelöscht oder falls möglich zurückgkgewiesen: (In 100% der Fälle zurückgewiesen falls sie alleiniger Empfänger der Email sind, sonst in ca. 60% gelöscht und in 40% zurückgewiesen)",
			    "Der Spam-Filter ist sehr stark eingestellt. Fast alle Werbe-Emails werden als solche identifiziert. Es kann vorkommen, dass eine normale Email als Werbemail identifiziert wird. Erkannte Werbe-Emails werden gelöscht oder falls möglich zurückgewiesen: (In 100% der Fälle zurückgewiesen falls sie alleiniger Empfänger der Email sind, sonst in ca. 80% gelöscht und in 20% zurückgewiesen)."
			    );
