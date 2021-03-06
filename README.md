# rkWFS (Version 3.0.2)
Simple Web-Filesystem

Copyright © 2017 rkCSD Eu <email@rkcsd.com>
Visit our website: http://www.rkcsd.com/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
MA 02110-1301, USA.

For running rkWFS, a web server with PHP >= 5.3 configured, is required.
You are free to change logo and skin of the application to use it for
your own company/organisation. If you've trouble running and using it,
we offer paid support. Contact us: +49 5631 9189488 / email@rkcsd.com

[Setup (German)](https://wiki.reneknipschild.net/comp:www:setuprkwfs)

### Base Installation

  * Rename **config.sample.php** to **config.php**
  * Set **WEBSITE_DEFAULT_URI** to the default URI
  * Add or remove Projects in **WFS_PROJECTS**

### Extended configuration

It's possible to set some other configurations in **config.php**:

  * **UPLOAD_FOLDER**: Specifies the place for all projects
  * **TEMP_FOLDER**: Needed for ZIP-Files. The webserver need access to it!
  * **ZIP_METHOD**: Can be __BUILD_IN__ (PHP's ZIPArchive, might be slow because compression cannot be disabled) or __LINUX_EXT__ (if the webserver runs under Linux, native zip-function will be used)
  * **MAX_ZIP_AGE**: Time, how long a autogenerated ZIP-Archive should exist
