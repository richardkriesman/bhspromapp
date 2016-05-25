<?php

    //This is the main configuration file for the site.
    //Ensure you add values for all of the variables. These are important.

    //The base URL of the application
    define('BASE_URL', 'https://example.com');

    //Color customization
    //You can choose from the following colors: blue, light_blue, cyan, teal, green, light_green, lime, yellow,
    //amber, orange, brown, blue_grey, grey, deep_orange, red, pink, purple, deep_purple, or indigo
    define('PRIMARY_COLOR', 'blue');
    define('ACCENT_COLOR', 'indigo');

    //The timezone you would like to use for times and dates.
    //A list of supported timezones can be found at http://php.net/manual/en/timezones.php
    define('TIME_ZONE', 'America/Chicago');
    define('DATE_FORMAT', 'F d, Y');
    define('DATETIME_FORMAT', 'F d, Y h:i A T');

    //
    // Active Directory LDAP Settings
    //
    define('LDAP_ENABLED', false); //Whether or not to enable LDAP auto-completion
    define('LDAP_HOST', 'LDAP://dc.example.lan'); //The hostname of the Domain Controller
    define('LDAP_DN', ''); //The DN of the Container or OU you want to search
    define('LDAP_SEARCH', 'samaccountname=*##ID_NUMBER##'); //The LDAP query to use to get the user - substitute the number with ##ID_NUMBER##
    define('LDAP_USERNAME', ''); //The username of the account used to authenticate with the DC
    define('LDAP_PASSWORD', ''); //The password of the account used to authenticate with the DC
