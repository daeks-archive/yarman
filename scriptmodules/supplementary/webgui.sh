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
    gitPullOrClone "$md_build/webgui" "https://github.com/daeks/RetroPie-WebGui"
}

function config_webgui() {
    sudo chown -R www-data:www-data "/var/www"
    sudo chmod -R 775 "/var/www/html"
    sudo usermod -a -G www-data pi
    
    rm "/var/www/html/index.html"
    cp "$md_build/webgui" "/var/www/html"
}

function remove_webgui() {
    rm -R "/var/www"
}
