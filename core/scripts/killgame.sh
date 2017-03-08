#!/usr/bin/env bash
kill $(pgrep -P $(pgrep -f "bash /opt/retropie/supplementary/runcommand/runcommand.sh"))
