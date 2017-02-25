# YARMan Web (Yet Another RetroPie Manager)

This is a webbased frontend for RetroPie containing several functions to maintain your setup. 

# Installation 

- Download scriptmodule to your local installation with

```curl https://raw.githubusercontent.com/daeks/yarman/master/scriptmodules/supplementary/yarman_default.sh -o ~/RetroPie-Setup/scriptmodules/supplementary/yarman.sh```
  
- Start RetroPie Setup and go to the experimental section for installing.
- Access YARMan from browser by http://retropie:8080/

# Contributions

Contributions to this project are welcome. Please follow the coding standard PSR2 before merging. Please note that the indent has to be 2 spaces instead of PSR2's default 4.

# (Optional) Installation with Apache

- Download scriptmodule to your local installation with

```curl https://raw.githubusercontent.com/daeks/yarman/master/scriptmodules/supplementary/yarman_apache.sh -o ~/RetroPie-Setup/scriptmodules/supplementary/yarman.sh```
  
- Start RetroPie Setup and go to the experimental section for installing.
- Access YARMan from browser by http://retropie:80/

# (Optional) Add Apache WWW share to SAMBA

To be able to access the www directory add the following lines to /etc/samba/smb.conf

```
[www]
comment = www
path = "/var/www/html"
writeable = yes
guest ok = yes
create mask = 0644
directory mask = 0755
force user = pi
hide dot files = no
```
