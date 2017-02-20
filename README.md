# RetroPie-WebGui

This is a webbased frontend for RetroPie containing several functions to maintain your setup. 

# Installation 

- Download scriptmodule to your local installation with

  curl https://raw.githubusercontent.com/daeks/RetroPie-WebGui/master/scriptmodules/supplementary/webgui.sh -o ~/RetroPie-Setup/scriptmodules/supplementary/webgui.sh
  
- Start RetroPie Setup and go to the experimental section for installing.

# Add www share to samba

To be able to access the www directory add the following lines to /etc/samba/smb.conf

  [www]
  comment = www
  path = "/var/www/html"
  writeable = yes
  guest ok = yes
  create mask = 0644
  directory mask = 0755
  force user = pi
  hide dot files = no