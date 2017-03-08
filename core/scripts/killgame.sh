#!/usr/bin/env bash
COMMANDINFO="/dev/shm/runcommand.info"
if [ -f "$COMMANDINFO" ]; then
   EMULATOR=$(sed -n 2p $COMMANDINFO)
   COMMAND=$(tail -1 $COMMANDINFO)
   PROGRAM=${COMMAND%% *}
   
   if [ "$PROGRAM" = "bash" ]; then
     PROGRAM=
     if [ "$EMULATOR" = "scummvm" ]; then 
      PROGRAM=$EMULATOR
     fi
     if [ "$EMULATOR" = "dosbox" ]; then 
      PROGRAM=$EMULATOR
     fi
   fi
   
   if [ "$PROGRAM" != "" ]; then
     PID=$(pgrep -n -f "$PROGRAM")
     if [ $PID -gt 0 ]; then
        kill -2 $PID
     fi
   fi
fi
