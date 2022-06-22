## Formatted online version of this README

https://www.drupal.org/project/limit_domain_access_by_role

## Whitelist technical domains to limit admin roles to interact only on those

It is pretty common that admin operations are only allowed on a **technical domain** (e.g. your-client.your-agency.com) that is different from the URL of the publically available site (e.g. your-client.com). This is mainly done for [security reasons](https://security.stackexchange.com/a/55443 "Is it reasonable to keep control panel in separate subdomain or domain? - Information Security Stack Exchange") as a technical domain can limit access, for example with [Htaccess password protection (aka shielding)](https://www.drupal.org/docs/develop/local-server-setup/linux-development-environments/set-up-a-local-development-drupal-0-8#s-password-protect-the-staging-server-but-not-local-or-production "What's more? | Develop guide on Drupal.org"). There might be other reasons to use a technical domain for admin operations, for example, to make it easier to exclude admin visits from the web analytics service. This module:

*   **adds a "permission" _Block access on non-technical domain_** (for the above-described use case, any role having a permission for an operation you do not want to be performed outside of the technical domain should be blocked)
*   **provides a configurable whitelist of technical domains** to limit admin roles to interact only on those.

## How it works

Whenever a login from an admin role is detected on a non-technical domain through [hook_user_login()](https://api.drupal.org/api/drupal/core%21modules%21user%21user.api.php/function/hook_user_login/8.8.x "hook_user_login | user.api.php | Drupal 8.7.x | Drupal API") that runs right after log in, it forces log out of the current user with the core function [user_logout | user.module | Drupal 8.7.x | Drupal API](https://api.drupal.org/api/drupal/core%21modules%21user%21user.module/function/user_logout/8.7.x). This will destroy the current session, and reset _$user_ to the anonymous user (uid=0). Other than normally resulting in an _Access denied_ page, no extra message is given to not give away info about the existence of a technical domain to a malicious user. Internally you should provide clear instructions to webmasters and content managers that they should always access through a technical domain.

## FAQs

### How to harden security even more?

Get even a higher level of security by defining the whitelisted domains in the _settings.php_ file instead of using the common configuration. This gets priority as it is a higher-level method (needs commit rights) and would disallow defining technical domains on the regular settings page, in that case being disabled for editing but still showing the detected values. This might be preferred it disallows users with an admin role changing the defined technical domain to the public one to circumvent the system. Add for example the following to the _settings.php_ file or a custom module (comma-separated, asterisk wildcards (\*) are allowed):

    $config['limit_domain_access_by_role.settings']['technical_domains'] = '*local*,*.dev,admin.example.com';

### Why not having a permission _Allow_ instead of _Block_?

One would expect the permission to be _**Allow** access on non-technical domain_ instead of _**Block** access on non-technical domain_ but doing it this way a user having multiple roles from which one with admin permissions would be blocked as expected. Furthermore, it avoids that the Administrator role can opt-out, having all permissions selected by default and grayed-out (not editable). This adds even more security if that is our use case.

### Not working on my local machine?

The configurable whitelist of technical domains does by default include _*local*_ and _*.dev_ to allow access on a local development environment. It accepts asterisk wildcards, so _*local*_ would allow access on e.g. _example.localhost_. Make sure to leave the defaults in place and add your own technical domain to the bottom of the list. The whitelist is only configurable for roles with the _"administer site configuration"_ permission.

### I am locked out. Now what?

The best is to define the whitelisted domains in the _settings.php_ file, as described above. If you have access to that (commit rights), you will never be locked out. You can define the used technical domain directly there. Furthermore, it is the highest level of security offered by the module. If you insist on using the regular settings instead, allowing admin users to change the used technical domains from the UI, then read on. Important when enabling the module, the superuser will also get blocked when not on a technical domain (UID #1 having the administrator role is getting all permissions by default). That means that **when enabling the module the correct technical domain should immediately be added in the whitelist** to avoid being locked out. So, to perform that task, **make sure you are logged in as an administrator user before enabling the module**. Also, export the configuration as usual and commit the modified configuration files. It can also be set through the terminal with drush (in case you are locked out): Then change: Note that linebreaks in the config form textarea field used between one domain and the other are turned into _\r\n_ when stored as a string (PHP_EOL). Related: [Set nano as a default editor over Vim - Super User](https://superuser.com/questions/864974/set-nano-as-a-default-editor-over-vim) (restart needed). Alternatively use [Drupal Console](https://drupalconsole.com "The Drupal CLI. A tool to generate boilerplate code, interact with and debug Drupal") that has an _editor_ option: Note that **_drush cset_ can not be used** due to a Drush bug [passing a literal string into drush config-set - Drupal Answers](https://drupal.stackexchange.com/questions/225734/how-to-pass-a-literal-string-into-drush-config-set/225739). Drupal Console ([config:override](https://drupalconsole.com/docs/en/commands/config-override "https://drupalconsole.com/docs/en/commands/config-override")) suffers a similar issue. After enabling the module and setting the technical domain, you might want to "throw out" all currently logged in users to force them to log in on the technical domain: Make sure you communicate in advance with all users with an admin role that they should use the technical domain.

### How to change the execution order?

If you have other authentication modules using _hook_user_login()_ and you want to control the execution order: [Changing the Order of Hooks in Drupal](https://x-team.com/blog/changing-the-order-of-hooks-in-drupal/)
