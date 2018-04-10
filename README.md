# PHP FRAMEWORK #

Currently in development, we hope to publish a stable version and its documentation soon.

The project was initially called "PHP Framework Partirle". The idea is to find a better name in the future.

If you have questions or discover something to improve or change, please contact us at https://github.com/dertin/framework-php/issues

At the moment, you can test it in a test environment and see how it works.

# INITIAL SETUP #

1. Go to the folder of your web server
    $ cd /var/www/htdocs

2. Download the latest version of the framework and unzip it to the root directory of your web server
    $ sudo wget https://codeload.github.com/dertin/framework-php/tar.gz/master -O framework-php.tar.gz
    $ sudo tar -xvzf framework-php.tar.gz --strip 1 && rm framework-php.tar.gz

4. Set the permissions of files and directories, as necessary
    $ sudo find . -type d -exec chmod 755 {} \;
    $ sudo find . -type f -exec chmod 644 {} \;
    $ sudo chmod 770 error.log && sudo chmod -R 770 Particle/Core/tmp

5. Edit the following configuration files:
    * [Configuration of the main database]
    - Particle/Apps/Settings/database.inc.php

    * [General configuration of the application, you can also add your own constants]
    - Particle/Apps/Settings/global.inc.php

6. Web server configuration

  * For servers with Apache: The redirection directives must be active, for the .htaccess file to work

  * For servers with Nginx: Edit your .conf file in Nginx

  - Configuration example:

    location ~ \.php$ {
  		include /etc/nginx/fastcgi.conf;
  		fastcgi_pass 127.0.0.1:9007;
  	}

  	location / {
      if (!-e $request_filename){
          rewrite ^(.+)$ /load.php?request=$1;
      }
    }

7. Install Composer (https://getcomposer.org/)

    * Install Application dependencies
    $ sudo cd Particle/Apps && composer install

    * Install Framework dependencies
    $ sudo cd Particle/Core && composer install

8. Ready. Now just visit yourwebsite.com to verify if everything is working correctly.


# Other configuration files #

* [Configuration Javascript and CSS resources for the layout]
- Particle/Apps/Views/layout/asset.ini

* [Configuration of constants for the templates]
- Particle/Apps/Views/layout/default/configs-smarty/host/default.conf
