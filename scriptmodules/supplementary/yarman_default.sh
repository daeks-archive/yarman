
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
rp_module_desc=" YARMan Web (Yet Another RetroPie Manager)"
rp_module_help="PHP and JQuery based web frontend on port 8080 for managing your retropie installation"
rp_module_section="exp"
rp_module_flags="noinstclean"

function depends_yarman() {
    local depends=(sqlite3 php5 php5-sqlite)
    isPlatform "x86" && depends=(sqlite php php-sqlite)
    getDepends "${depends[@]}"
}

function sources_yarman() {
    gitPullOrClone "$md_build" "https://github.com/daeks/yarman"
}

function install_yarman() {
    killall php
    if [ -d "$md_inst/data" ]; then
      cp $md_inst/data/*.sdb "$md_build/data"
      cp $md_inst/data/*.bak "$md_build/data"
    fi
    rm -r "$md_inst"
    cp -r "$md_build/." "$md_inst"
}

function configure_yarman() {    
    echo $user > "$md_inst/data/user"
    php -S 0.0.0.0:8080 -t "$md_inst" > /dev/null 2>&1 &

    local config="php -S 0.0.0.0:8080 -t \"$md_inst\" > /dev/null 2>\&1 \&"
    sed -i "/^php/d" /etc/rc.local
    sed -i "s|^exit 0$|${config}\\nexit 0|" /etc/rc.local
}

function remove_yarman() {
    killall php
    sed -i "/^php/d" /etc/rc.local
    rm -R "$md_inst"
}
