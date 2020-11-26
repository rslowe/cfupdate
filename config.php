<?php
    ############# DO NOT EDIT THE BELOW SECTION #############
    $__CONFIGFILELOC = __FILE__;
    $__CONFIGFILECALL = $_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF'];
    if(substr($_SERVER['DOCUMENT_ROOT'], -1) == '/') {
        $__CONFIGFILECALL = substr($_SERVER['DOCUMENT_ROOT'], 0, -1).$_SERVER['PHP_SELF'];
    }
    if($__CONFIGFILELOC == $__CONFIGFILECALL) { 
        header("status: 204");
        header("HTTP/1.0 204 No Response");
        die();
    }
    ############# DO NOT EDIT THE ABOVE SECTION #############

    ############# NOTICE TO IMPLEMENTER, THIS MEANS YOU! #############
    // Are you on Apache?
    // Did you check the .htaccess and .htpasswd files?
    // If you don't want them, remove both .htpasswd and .htaccess
    ############# NOTICE TO IMPLEMENTER, THIS MEANS YOU! #############

    $baseUrl      = 'https://api.cloudflare.com/client/v4/';    // The URL for the CloudFlare API, Change if an Update is Pushed by CF.

    $hosts = array(
	    "KEY_GOES_HERE" => "ENTER_EVERYTHING_BEFORE_THE_DOMAIN",
    );

    $apiKey       = "<CLOUDFLARE_GLOBAL_API_KEY>";                   // Your CloudFlare API Key.
    $apiToken     = "<CLOUDFLARE_API_TOKEN_FOR_ZONE>";               // Cloudflare API Token (With Write Permissions to DNS Zone)
    $myDomain     = "example.net";                                   // Your domain name.
    $emailAddress = "exampleemailaccount@example.net";               // The email address of your CloudFlare account.

    $useApiToken = FALSE;                                            // Use an API Token instead of the Global API Key.
    $v6v4ExclusiveModeEnable = FALSE;                                // Prevent ipv4 and ipv6 records from mixing.
    $v4OnlyPrefix = "ip4.";                                          // String (in domain) to ensure v4 only entry. (must end in .) (Only if $v6v4ExclusiveModeEnable = TRUE) 
    $v6OnlyPrefix = "ip6.";                                          // String (in domain) to ensure v6 only entry. (must end in .) (Only if $v6v4ExclusiveModeEnable = TRUE) 
    

?>
