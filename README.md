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

2. Clone the pocket_php repository and set the server permissions
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
   
   ---- NOTE ----
   Web server configuration is a broad topic, the following setup is intended to be customized for it's intended purpose as an exmaple and
   is only a basic gestalt of what POCKET_PHP requires to work. For development environments extra precautions should be taken
   like using a dedicated user and group combo to isolate <a href="https://nginx.com" class="white">NGINX</a> and PHP to their respective working folders only.
   This is specially true for (securely) serving through TOR!

   As long as your webserver of choice respects the simple rules below, pocket_php will work with it.
   
   ```sh
   1. Serve static files directly
   2. Redirect everything else to /app/index.php
   ```
   
   The provided <a href="{{nginx_vbs}}">virtual server file</a> for <a href="https://nginx.com" class="greentext">NGINX</a> also adds a few security filters to keep some static files (such as the internal DB) private.
   As a side note, there have been some issues with the way php-fpm handles sqlite databases that share the same name but are from independent projects, a very common case when
   running multiple websites from a single server, simply rename the database file and update the location constant in configure.php.
   
   Create the NGINX configuration folder structure, change the permissions and move the included config files.
   
    ```sh
    $ sudo chown -R user:group /etc/nginx/
    $ sudo chown -R 755 /etc/nginx
    $ mkdir /etc/nginx/ssl
    $ mkdir /etc/nginx/sites-enabled
    $ mkdir /etc/nginx/sites-available
    $ mv /var/web_server/pocket_php/static/text_files/nginx_config /etc/nginx/nginx.conf
    $ mv /var/web_server/pocket_php/static/text_files/nginx_pocket_php_vsb /etc/nginx/sites-available/default
    $ sudo ln -s /etc/nginx/sites-available /etc/nginx/sites-enabled
    $ sudo systemctl restart nginx.service 
    ```
    
    Uncomment the SSL Settings block and modify the following lines in the included nginx.conf with your own
   
    
    ```sh
    ssl_certificate     /etc/nginx/ssl/cert.crt
    ssl_certificate_key /etc/ngins/ssl/key.key
    ```

    Then, uncomment the HTTPS ENABLED (and comment out the HTTPS DISABLED) block in sites-available/default. 

4. Configure PHP

    The only relevant changes are to the www.conf and php.ini files. However, POCKET_PHP internally modifies some of the php.ini settings, others must be manually set in php.ini.
 
    In /etc/php/php.ini 
 

    ```sh
    - Uncomment the extension=pdo_sqlite and extension=gd
    - Change the default session.name (for security reasons)
    - Modify the the file upload settings to match your application's needs (the settings required are specified in app/configure.php)
    ```
    
  `/etc/php/php-fpm.d/www.conf`
  
  ```sh
  1. Change user and group to ones NGINX can access
  ```

5. Configure Pocket_PHP

    All the relevant configuration lies in app/configure.php, note that the pocket_php/tools/ directory holds the sqlite database file, the sqlite file itself and the sessions management folder must be writeable by the web server.

    In /etc/php/php.ini 
    ```sh
    $ mkdir /var/web_server/pocket_php/tools/sessions/
    $ sudo chown -R username:group /var/web_server/pocket_php/tools/
    $ sudo chmod -R 755 /var/web_server/pocket_php/tools/pocket_php.db
    ```
    Finally, the configure.php file overrides the php.ini settings that POCKET_PHP depends on, this prevents clashing between virtual servers with different settings and the main php.ini defaults. It's strongly suggested to modify the included configure file instead.

</details>


## Included Example website and documentation
Pocket_PHP comes with an example landing site that serves as its main documentation.

For more information visit the official project site at [XENOBYTE.XYZ](https://xenobyte.xyz/projects/?nav=pocket_php)
