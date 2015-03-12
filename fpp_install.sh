#!/bin/bash
pushd $(dirname $(which $0))
apt-get -y update
apt-get -y install apt-get install php5 apache2 libgd2-xpm libgd2-xpm-dev libgd2-dev libpng12-dev libjpeg62-dev libgd-tools libpng12-dev libgd2-xpm libgd2-xpm-dev libssl-dev gnutls-bin iputils
groupadd www-data
#groupadd nagios
#adduser nagios
#usermod -G nagios nagios
#usermod -G www-data,nagios www-data
#mkdir /usr/local/nagios
apt-get install nagios3
#chown -R nagios:nagios /usr/local/Nagios
cd /tmp
wget http://www.boutell.com/gd/http/gd-2.0.33.tar.gz
tar -zxvf gd-2.0.33.tar.gz
cd /tmp/gd-2.0.33
./configure
make && make install
cd /tmp
#wget http://prdownloads.sourceforge.net/sourceforge/nagios/nagios-3.4.1.tar.gz
#tar xzf nagios-3.4.1.tar.gz
#cd /tmp/nagios
#./configure –prefix=/usr/local/nagios –with-cgiurl=/nagios/cgi-bin –with-htmurl=/nagios/ –with-nagios-user=nagios –with-nagios-group=nagios –with-command-group=nagios
#./configure
#make all
#make install
#make install-init
#make install-commandmode
#make install-config
#make install-webconf
#make install-classicui
cd /tmp
apt-get install nagios-plugins nagios-snmp-plugins
#make install-exfoliation
#- This installs the Exfoliation theme for the Nagios web interface
 /etc/init.d/apache2 reload
 service nagios start
popd
