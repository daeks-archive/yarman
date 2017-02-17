#!/usr/bin/env bash

# This file is part of The RetroPie Project
#
# The RetroPie Project is the legal property of its developers, whose names are
# too numerous to list here. Please refer to the COPYRIGHT.md file distributed with this source.
#
# See the LICENSE.md file at the top-level directory of this distribution and
# at https://raw.githubusercontent.com/RetroPie/RetroPie-Setup/master/LICENSE.md
#

rp_module_id="webscraper"
rp_module_desc="WebScraper GUI"
rp_module_section="exp"

function depends_webscraper() {
    getDepends apache2 sqlite3 php5 php5-sqlite 
}

function remove_share_samba() {
    local name="$1"
    [[ -z "$name" || ! -f /etc/samba/smb.conf ]] && return
    sed -i "/^\[$name\]/,/^force user/d" /etc/samba/smb.conf
}

function add_share_samba() {
    local name="$1"
    local path="$2"
    [[ -z "name" || -z "$path" ]] && return
    remove_share_samba "$name"
    cat >>/etc/samba/smb.conf <<_EOF_
[$1]
comment = $name
path = "$path"
writeable = yes
guest ok = yes
create mask = 0644
directory mask = 0755
force user = $user
_EOF_
}

function restart_webscraper() {
    service apache2 restart
}

function config_webscraper() {

    sudo chown -R www-data:www-data "/var/www"
    sudo chmod -R 775 "/var/www/html"
    sudo usermod -a -G www-data pi
    
    cp /etc/samba/smb.conf /etc/samba/smb.conf.bak
    add_share_samba "www" "/var/www"
    restart_webscraper
}

function remove_webscraper() {
    local name
    for name in www; do
        remove_share_samba "$name"
    done
    rm -R "/var/www"
}

function gui_webscraper() {
    while true; do
        local cmd=(dialog --backtitle "$__backtitle" --menu "Choose an option" 22 76 16)
        local options=(
            1 "Install WebScraper GUI"
            2 "Restart WebScraper service"
            3 "Remove WebScraper GUI + configuration"
        )
        local choice=$("${cmd[@]}" "${options[@]}" 2>&1 >/dev/tty)
        if [[ -n "$choice" ]]; then
            case $choice in
                1)
                    rp_callModule "$md_id" depends
                    rp_callModule "$md_id" config
                    printMsgs "dialog" "Installed and enabled WebScraper"
                    ;;
                2)
                    rp_callModule "$md_id" restart
                    ;;
                3)
                    rp_callModule "$md_id" depends remove
                    rp_callModule "$md_id" remove
                    printMsgs "dialog" "Removed WebScraper service"
                    ;;
            esac
        else
            break
        fi
    done
}
