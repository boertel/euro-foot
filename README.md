euro-foot
=========

FACEBOOK DEV CONF FOR MAC
=========================

Add the following line to your /etc/conf

127.0.0.1 euro-foot.local

Add the following vhost to your apache

<VirtualHost *:80>
    DocumentRoot "/Users/arthur/Sites/euro-foot"
    ServerName euro-foot.local
    ServerAlias euro-foot.local
    ErrorLog "/private/var/log/apache2/euro-foot.local-error_log"
    CustomLog "/private/var/log/apache2/euro-foot.local-access_log" common
</VirtualHost>
