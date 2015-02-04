#!/bin/bash

export DEBIAN_FRONTEND=noninteractive

function install() {
  update_distro
  install_core
  install_mysql
  install_apache2
  install_php5
  install_phpmyadmin
  install_roundcube
  install_roundcube_plugins

  service apache2 restart
}

function update_distro() {
  apt-get update
  aptitude -y full-upgrade
}

function install_core() {
  apt-get install -y curl build-essential git tmux htop
}

function install_apache2() {
  apt-get install -y apache2
}

function install_php5() {
  apt-get install -y php5-cli php5-mcrypt libapache2-mod-php5

  ln -s /etc/php5/mods-available/mcrypt.ini /etc/php5/apache2/conf.d/
}

function install_mysql() {
  echo 'mysql-server mysql-server/root_password password vagrant'       | sudo debconf-set-selections
  echo 'mysql-server mysql-server/root_password_again password vagrant' | sudo debconf-set-selections
  apt-get install -y mysql-server mysql-client
}

function install_phpmyadmin() {
  echo 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2' | debconf-set-selections
  echo 'phpmyadmin phpmyadmin/mysql/admin-pass password vagrant'         | debconf-set-selections
  echo 'phpmyadmin phpmyadmin/mysql/app-pass password vagrant'           | debconf-set-selections
  apt-get install -y phpmyadmin
}

function setup_roundcube_db_config() {
  cat > /etc/dbconfig-common/roundcube.conf << EOF
dbc_dbadmin='root'
dbc_dbname='roundcube'
dbc_dbpass='vagrant'
dbc_dbtype='mysql'
dbc_dbuser='root'
dbc_install='true'
dbc_upgrade='true'
EOF
}

function install_roundcube() {
  echo "dbconfig-common dbconfig-common/mysql/admin-pass password vagrant" | debconf-set-selections
  setup_roundcube_db_config
  echo 'roundcube-core roundcube/dbconfig-install boolean true'     | debconf-set-selections
  echo 'roundcube-core roundcube/database-type select mysql'        | debconf-set-selections
  echo 'roundcube-core roundcube/mysql/admin-pass password vagrant' | debconf-set-selections
  # Sometimes, setting the language doesn't work.
  # Rerun this line and run # dpkg-reconfigure roundcube-core
  echo 'roundcube-core roundcube/language select en_US'             | debconf-set-selections
  apt-get install -y roundcube
  ln -s /var/lib/roundcube /var/www/html/roundcube
}

function install_roundcube_plugins() {
  ln -s /vagrant/plugins/mailforward /var/lib/roundcube/plugins/
  ln -s /vagrant/plugins/spamsettings /var/lib/roundcube/plugins/
  rm /etc/roundcube/main.inc.php
  cp /vagrant/config/main.inc.php /etc/roundcube/
  chown root:www-data /etc/roundcube/main.inc.php
}

install
