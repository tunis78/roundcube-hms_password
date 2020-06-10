hMailServer Remote Password Plugin for Roundcube
==========================================
Plugin that adds a possibility to change users password using
remote method (driver) to build-in password plugin.

Requirements
------------
- hMailServer with webserver and PHP >= 5.3.0
- Requires PHP COM (Windows only)

How to install
--------------
- Copy folder to *roundcube/plugin/password*
- Config *config.inc.php.dist* and save as *config.inc.php*
- Enable password plugin in *roundcube/config/config.inc.php*

Drivers
-------
hMailServer Remote Password plugin adds support of change mechanisms which are handled
by included drivers. Just pass driver name in 'password_driver' option.

**hMailServer Remote (hmail_remote)**

Requires PHP COM (Windows only) on remote host.
See config.inc.php.dist file for more info under hMail remote Driper options.

Changelog
---------
Version 1.2 (2020-06-10)
- First release on github

License
-------
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see http://www.gnu.org/licenses/.