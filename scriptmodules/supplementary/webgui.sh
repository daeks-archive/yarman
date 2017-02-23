
#!/usr/bin/env bash

# This file is part of The RetroPie Project
#
# The RetroPie Project is the legal property of its developers, whose names are
# too numerous to list here. Please refer to the COPYRIGHT.md file distributed with this source.
#
# See the LICENSE.md file at the top-level directory of this distribution and
# at https://raw.githubusercontent.com/RetroPie/RetroPie-Setup/master/LICENSE.md
#

rp_module_id="webgui"
rp_module_desc="RetroPie WebGUI"
rp_module_section="exp"

function depends_webgui() {
    getDepends apache2 sqlite3 php5 php5-sqlite
}

function sources_webgui() {
    gitPullOrClone "$md_build" "https://github.com/daeks/RetroPie-WebGui"
}

function install_webgui() {
    if [ -d "/var/www/html/data" ]; then
      cp -r "/var/www/html/data/." "$md_build/data"
    fi
    rm -rf "/var/www/html/*"
    cp -r "$md_build/." "/var/www/html"
}

function configure_webgui() {
    chown -R $user:$user "/var/www"
    chmod -R 775 "/var/www/html"
    usermod -a -G www-data $user
    sed -i "s/export APACHE_RUN_USER=www-data/export APACHE_RUN_USER=$user/g" /etc/apache2/envvars
    sed -i "s/export APACHE_RUN_GROUP=www-data/export APACHE_RUN_GROUP=$user/g" /etc/apache2/envvars
    service apache2 restart
}

function remove_webgui() {
    aptRemove apache2 sqlite3 php5 php5-sqlite
    rm -R "/var/www"
}
