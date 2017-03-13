#!/bin/bash
# yarman.sh
###########
#
# The yarman's manager.
#
# The yarman can not be started directly by the root user, but
# but if you need it (ex.: on boot), use the --user option. The user must
# be a RetroPie user (must have a RetroPie directory tree in its homedir).
# If it's called in a "sudo environment", it's OK, the sudo user will start
# the service.
#
# The default TCP port is 8080, but the user can define another port using
# the --port or -p option (allowed ports are between 1024 and 65535, inclusive).
# This script doesn't allow to start multiple instances of yarman,
# even if the user try to start it listening to a different port.
#
# Execute it with --help to see the available options.
#

# global variables ##########################################################

yarman_dir="/opt/retropie/supplementary/yarman"

usage="$(basename $0) OPTIONS"

help_message="Usage: $usage

The OPTIONS are:

-h|--help           print this message and exit

--start             start the yarman

--stop              stop the yarman

--isrunning         show if yarman is running and the
                    listening port and exit

-p|--port NUMBER    make yarman listen at port NUMBER (optional,
                    default: 8080, only works with --start)

-u|--user USER      start yarman as USER (only available for
                    privileged users, only works with --start, USER must 
                    be a RetroPie user)

The --start and --stop options are, obviously, mutually exclusive. If the
user uses both, only the first works."

# default TCP port to listen
port=8080

# getting the caller username
user="$SUDO_USER"
[[ -z "$user" ]] && user=$(id -un)



##############################################################################
# Set the user that will start the yarman. Only privileged users
# can use this function. The user MUST have a RetroPie directory tree in
# its homedir.
#
# Globals:
#   user
# Arguments:
#   $1  a valid RetroPie user name
# Returns:
#   0, if a the user is setted correctly
#   non-zero, otherwise
##############################################################################
function set_user() {
    if [[ $(id -u) -ne 0 ]]; then
        echo "Error: only privileged users (ex.: root) can use --user option." >&2
        return 1
    fi

    if [[ ! -d "/home/$1/RetroPie/" ]]; then
        echo "Error: the user '$1' is not a RetroPie user." >&2
        return 1
    fi

    user="$1"
    return 0
}


##############################################################################
# Set the listening TCP port. The valid ports to use are between
# 1024 and 65535, inclusive.
#
# Globals:
#   port
# Arguments:
#   $1  a TCP port number
# Returns:
#   0, if the port is correctly setted
#   non-zero, otherwise
##############################################################################
function set_port() {
    # checking if $1 is a number and is a valid non-privileged port
    if [[ "$1" =~ ^[[:digit:]]+$ ]]; then
        if [[ "$1" -lt 1024 || "$1" -gt 65535 ]]; then
            echo "Error: invalid port number: $1" >&2
            echo "The port must be a number between 1024 and 65535, inclusive" >&2
            return 1
        fi
    fi

    port="$1"
}



##############################################################################
# Check if yarman is running. If positive, fill the $port global
# variable with current listening port.
#
# Globals:
#   port
# Arguments:
#   None
# Returns:
#   0, if yarman is running
#   non-zero, otherwise
##############################################################################
function is_running() { 
    local return_value

    pgrep -f 'php -S.*yarman' &>/dev/null || return $?
    return_value=$?

    # ok... maybe there is a more elegant way to obtain the current
    # listening port, but let's use this way for a while.
    port=$(
        ps ax \
        | grep -m 1 -o 'php -S.*yarman' \
        | cut -d' ' -f3 \
        | cut -d: -f2
    )
    return $return_value
}


##############################################################################
# Starts the yarman service. It gives an error if it's called
# directly by the root user, but if it's called by an "sudo environment",
# yarman is started by the sudo user.
#
# Globals:
#   user
# Arguments:
#   None
# Returns:
#   1, if it's unable to start yarman
#   0, if yarman is successfully started
##############################################################################
function start_service() {
    if is_running; then
        echo "Nothing done. yarman is already running and listening at $port." >&2
        return 1
    fi

    local startcmd=( php -S "0.0.0.0:$port" -t "${yarman_dir}" )

    # yarman should not be started directly by the root user,
    # but we can deal if it's called by a "sudo environment" or with
    # the --user option.
    if [[ $(id -u) -eq 0 ]]; then
        if [[ $(id -u "$user") -eq 0 ]]; then
            echo "Error: yarman can't be started directly by root!" >&2
            echo "Try to use '--user' option" >&2
            return 1
        fi
        startcmd=( su -c "'${startcmd[@]}'" "$user" )
    fi

    "${startcmd[@]}" &> /dev/null &
    echo "Starting yarman listening at port $port..."
    sleep 3
    if is_running; then
        echo "yarman is running and listening at port $port"
        return 0
    else
        echo "Error: It seems that yarman had some problem to start!" >&2
        return 1
    fi
}


##############################################################################
# Stops the yarman service if it's running.
#
# Globals:
#   None
# Arguments:
#   None
# Returns:
#   0, if yarman process is successfully killed
#   1, if unable to kill yarman
#   2, if yarman wasn't running from the start
##############################################################################
function stop_service() {
    if is_running; then
        echo "Stopping yarman..."
        sudo kill -9 $(pgrep -f 'php -S.*yarman')
        sleep 1
        if is_running; then
            echo "Error: Unable to kill yarman process." >&2
            return 1
        else
            echo "yarman has been stopped."
            return 0
        fi
    fi

    echo "Nothing done. yarman wasn't running." >&2
    return 2
}


# starting point #############################################################

if [[ -z "$1" ]]; then
    echo "Error: missing arguments" >&2
    echo "$help_message" >&2
    exit 1
fi


# the following variables work like flags. they are used to deal with 
# the command line options.
f_start=0
f_stop=0

while [[ "$1" ]]; do
    case "$1" in
        -h|--help)
            echo "$help_message"
            exit 0
            ;;

        --isrunning)
            if is_running; then
                echo "yarman is running and listening at port $port"
                exit 0
            else
                echo "yarman is not running"
                exit 1
            fi
            ;;

        --start)
            if [[ "$f_stop" == "1" ]]; then
                echo "Warning: ignoring '--start' option" >&2
                f_start=0
            else
                f_start=1
            fi
            ;;

        --stop)
            if [[ "$f_start" == "1" ]]; then
                echo "Warning: ignoring '--stop' option" >&2
                f_stop=0
            else
                f_stop=1
            fi
            ;;

        --port|-p)
            if [[ "$f_start" = "0" ]]; then
                echo "Error: the '--port' option is used with '--start' only" >&2
                exit 1
            fi
            shift
            set_port "$1" || exit $?
            ;;

        -u|--user)
            if [[ "$f_start" = "0" ]]; then
                echo "Error: the '--user' option is used with '--start' only" >&2
                exit 1
            fi
            shift
            set_user "$1" || exit $?
            ;;

        *)
            echo "Invalid option: $1" >&2
            exit 1
            ;;
    esac

    # shifting for the next option
    shift
done

if [[ "$f_start" = "1" ]]; then
    start_service
    exit $?
fi

if [[ "$f_stop" = "1" ]]; then
    stop_service
    exit $?
fi
