euro-foot
=========

FACEBOOK DEV CONF FOR MAC
-------------------------

#Add the following line to your /etc/conf

    127.0.0.1 euro-foot.local

#Add the following vhost to your apache

    <VirtualHost *:80>
        DocumentRoot "/Users/arthur/Sites/euro-foot"
        ServerName euro-foot.local
        ServerAlias euro-foot.local
        ErrorLog "/private/var/log/apache2/euro-foot.local-error_log"
        CustomLog "/private/var/log/apache2/euro-foot.local-access_log" common
    </VirtualHost>

FACEBOOK DEV CONF FOR WINDOWS
-----------------------------

#Add the following line to your C:\Windows\System32\drivers\etc\hosts

    127.0.0.1 euro-foot.local

#With wamp add a file (whatever the name) in C:\wamp2\alias\ (assuming you installed wamp in c:\wamp2) with this content :

    <VirtualHost euro-foot.local>
	DocumentRoot "C:/wamp2/www/euro-foot"
	ServerName euro-foot.local
	ServerAlias euro-foot.local
	ErrorLog "C:/wamp2/logs/euro-foot.local-error_log"
	CustomLog "C:/wamp2/logs/euro-foot.local-access_log" common
    </VirtualHost>

The file will be automaticly included in your httpd.conf

