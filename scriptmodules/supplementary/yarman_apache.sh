
#!/usr/bin/env bash

# This file is part of The RetroPie Project
#
# The RetroPie Project is the legal property of its developers, whose names are
# too numerous to list here. Please refer to the COPYRIGHT.md file distributed with this source.
#
# See the LICENSE.md file at the top-level directory of this distribution and
# at https://raw.githubusercontent.com/RetroPie/RetroPie-Setup/master/LICENSE.md
#

rp_module_id="yarman"
rp_module_desc=" YARMan Web (Yet Another RetroPie Manager) on port 80"
rp_module_help="PHP and JQuery based web frontend for managing your retropie installation"
rp_module_section="exp"

function depends_yarman() {
    getDepends apache2 sqlite3 php5 php5-sqlite
}

function sources_yarman() {
    gitPullOrClone "$md_build" "https://github.com/daeks/yarman"
}

function install_yarman() {
    if [ -d "/var/www/html/data" ]; then
      cp -r "/var/www/html/data/." "$md_build/data"
    fi
    rm -rf "/var/www/html/*"
    cp -r "$md_build/." "/var/www/html"
}

function configure_yarman() {
    chown -R $user:$user "/var/www"
    chmod -R 775 "/var/www/html"
    sed -i "s/export APACHE_RUN_USER=www-data/export APACHE_RUN_USER=${user}/g" /etc/apache2/envvars
    sed -i "s/export APACHE_RUN_GROUP=www-data/export APACHE_RUN_GROUP=${user}/g" /etc/apache2/envvars
    systemctl daemon-reload
    service apache2 restart
}

function remove_yarman() {
    rm -rf "/var/www/html/*"
}
