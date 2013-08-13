= MLIB-Inventory-Server =
=======================

Introduction
------------
Restful service for handling computer asset management.


Installation
------------
To install simply run the 'install' file located within the 'install' folder

Virtual Host
------------
An example...

```
<VirtualHost *:80>
    ServerName inventory.dev
    DocumentRoot /var/www/html/MLIB-Inventory-Server/public
    SetEnv APPLICATION_ENV "development"
    <Directory /var/www/html/MLIB-Inventory-Server/public>
        DirectoryIndex index.php
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>
    ErrorLog /var/log/httpd/inventory_error
    CustomLog /var/log/httpd/inventory_log common
</VirtualHost>
```