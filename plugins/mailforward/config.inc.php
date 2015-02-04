<?php

$rcmail_config['mailforward_ldap_host'] = 'localhost';

// LDAP server port to connect to
// Default: '389'
$rcmail_config['mailforward_ldap_port'] = '389';

// LDAP version
// Default: '3'
$rcmail_config['mailforward_ldap_version'] = '3';

// LDAP base name (root directory)
// Exemple: 'dc=exemple,dc=com'
$rcmail_config['mailforward_ldap_basedn'] = 'dc=company,dc=ch';

// LDAP Admin DN
// Default: value
$rcmail_config['mailforward_ldap_adminDN'] = 'cn=admin,dc=company,dc=ch';

// LDAP Admin Password
// Default: value
$rcmail_config['mailforward_ldap_adminPW'] = '';


$rcmail_config['mailforward_ldap_user_search_attribute'] = 'mail';


