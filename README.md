php-nimblecrm-api
=================

PHP implementation to connect to Nimble CRM API

I haven't tested the index.php demo page, as I've implemented my code within a Symfony 1.4 plugin first, and I've just extracted the nimble auth code to provide you guys. Do not hesistate to do a pull request if you find errors.

> Note: Don't forget to change the config in the __construct method.


Methods
-------

**requestAuthGrantCodeUrl:** URL to visit in order to login and allow Nimble to share data with your app.

**requestAccessToken:** Once you've allowed the previous thing, this will generate an access_token.

**getContactList:** Getting the stuff for real. Now that you got access token, this will retrieve your contact list (well, at least first page). If this works, you're good to go.
