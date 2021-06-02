# [Pocket_PHP](https://xenobyte.xyz/projects/?nav=pocket_php)
​

Pocket_php is an MVC framework for PHP7+ that emphasizes performance and simplicity.

By abstracting away many of PHP7's shortcomings and implementing an MVC design pattern, the framework provides a safer development and production environment with virtually no performance costs, the entire source code can be read in twenty minutes and was designed with extensibility and mutability in mind to fit any given project that would otherwise require deep modifications of unnecessarily large "solutions" or for the programmer to write generic code that could be prone to security flaws.


It is primarily built for the NGINX server to take full advantage of its scalabilty and performance allowing fully featured web projects to be run on budget hardware. A largely unexploited benefit of modern VPS providers that goes ignored due to the bloated minimal requirements of the modern backend libraries we've (erroneously) learned to depend on.


Pocket_php is particularly well suited for hidden services running on budget hardware!

## Features


* MVC implentation automatically limits client access to controller files
* HTML template engine for full frantend & backend separation
* Fully featured session manager with php.ini independent timeout and expiration controls
* User input sensitization
* Internal IP tracking & banning
* Cross*platform, runs on anything that can host a web server
* Included NGINX configuration
* Scalable and easily modified
* Tiny code base providing the very essentials
* 100% independent & efficient CAPTCHA included
* Lowest performance hit you've seen or your money back
* It's fucking free
* MIT Licensed

## Dependencies
* [PHP7+](https://php.net)
* [SQLite](https://sqlite.org) 
* [NGINX](https://nginx.com) 

## Installation
<details><summary><b>Show instructions</b></summary>

1. Install PHP7+ and NGINX
   For arch / manjaro: 

    ```sh
    $ sudo pacman -S nginx php php-fpm php-fpm php-sqlite php-gd sqlitebrowser sqlite
    ```

2. Clone the pcoket_php repository and set the server permissions
   Note that both the webserver and the php-fpm daemon must have read & write permissions on the project folder.

    ```sh
    $ git clone https://git.xenobyte.xyz/XENOBYTE/pocket_php/
    $ sudo mkdir /var/web_server
    $ sudo chown -R username:group /var/web_server_php
    $ sudo chmod -R 755 /var/web_server
    $ mv -r pocket_php /var/web_server/
    $ sudo chown -R username:group /var/web_server/pocket_php
    $ sudo chmod -R 755 /var/web_server/pocket_php 
    ```

3. Configure NGINX
   Remember to modify the provided nginx virtual server configuration file to match your desired settings.

    ```sh
    $ sudo mv /var/web_server/pocket_php/static/text_files/nginx_config /etc/nginx/nginx.conf
    $ sudo mv /var/web_server/pocket_php/static/text_files/nginx_pocket_php_vsb /etc/nginx/sites-available/default
    $ sudo systemctl restart php-fpm.service
    $ sudo systemctl restart nginx.service 
    ```
    Uncomment the SSL Settings block and modify the following lines in the included nginx.conf with your own
    
    ```sh
    ssl_certificate     /etc/nginx/ssl/cert.crt
    ssl_certificate_key /etc/ngins/ssl/key.key
    ```
   
4. Configure PHP
   The only relevant changes are to the www.conf and php.ini files.
 

    ```sh
    1. Uncomment the extension=pdo_sqlite and extension=gd 
    2. Change the default session.name (for security reasons)
    3. Modify the upload.limit to fit your needs
    4. Set the desired time zone in "date.timezone" (NOTE: this setting can and usually is overwritten)
    ```
    
  `/etc/php/php-fpm.d/www.conf`
  
  ```sh
  1. Change user and group to ones NGINX can access
  ```

5. Configure Pocket_PHP
   All the relevant configuration lies in app/configure.php, note that the core directory holds the sqlite database file and thus both the file and folder must be writeable by the web server.

    ```sh
    $ sudo chown -R username:group /var/web_server/pocket_php/core/
    $ sudo chmod -R 755 /var/web_server/pocket_php/tools/pocket_php.db
    ```
    It's also worth mentioning that locale settings used by PHP are the same enabled in the host system and that the
    default timezone can be set in the php.ini file and overwritten in the configure.php source. Just an FYI.

</details>


## Webserver configuration
  As long as your webserver of choice respects the simple rules below, pocket_php will work with it.
  ```sh
  1. Serve static files directly
  2. Redirect everything else to /app/index.php
  ```
  The provided virtual server file for NGINX also adds a few security filters to keep some static files (such as the internal DB) private. As a side note, there have been some issues with the way php-fpm handles sqlite databases that share the same name but are from independent projects, a very common case when running multiple websites from a single server, simply rename the database file and update the location constant in configure.php. 

## Included Example website and documentation
Pocket_PHP comes with an example site and user guide that serves as its main documentation.

<p align="center"><img src="https://i.imgur.com/NjnKWy4.jpg" /></p>

See the "user guide" section for a more thorough explanation.
 
For more information visit the official project site at [XENOBYTE.XYZ](https://xenobyte.xyz/projects/?nav=pocket_php)
