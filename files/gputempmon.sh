#!/bin/sh
#akpool.org
#latest version at http://akpool.org/files/gputempmon.sh
#version 0.56
#this script monitors temperatures across cards and adjusts MHZ and fan speeds as necessary
CARD0ENABLE=1
CARD1ENABLE=1
CARD2ENABLE=1
CARD3ENABLE=0
#clock down to MINCLOCK when OVERHEATTEMP is reached
#additionally set fanspeed to 95%
#basically card is getting a little warm lets set it up to a regular clock rate
OVERHEATTEMP=82
MINCLOCK=900
#clock up to MAXCLOCK if temperature is under COOLTEMP
#additionally set fan to auto
COOLTEMP=74
MAXCLOCK=980
MAXMEMORY=1120
#when between TARGETTEMPLOW and TARGETTEMPHIGH
#set clock to TARGETCLOCK
#additinally set fan speed to auto
#TARGETTEMPLOW=75
#TARGETTEMPHIGH=80
#TARGETCLOCK=880
MEMORYCLOCK=1050

aticonfig --od-enable


while true; do
sleep 5;

if [ "$CARD0ENABLE" == "1" ]; then
export DISPLAY=:0
num=`aticonfig --pplib-cmd "get temperature 0"|tr -s ' '|cut -f 7 -d ' '|cut -f 1 -d '.'`
if [ "$num" -gt "$OVERHEATTEMP" ]; then
echo "Adapter 0 is overheating clocking it down and increasing fan to 100%"
aticonfig --od-setclocks=$MINCLOCK,$MEMORYCLOCK --adapter=0;
aticonfig --pplib-cmd "set fanspeed 0 95"
echo $num
fi
if [ "$num" -lt "74" ]; then
echo "Clocking up adapter 0"
CURRENTCLOCK=`aticonfig --od-getclocks --adapter=0|grep "Current Clocks"|tr -s ' '|cut -f 5 -d ' '`
if [ ! "$CURRENTCLOCK" -eq "$MAXCLOCK" ]; then
aticonfig --od-setclocks=$MAXCLOCK,$MAXMEMORY --adapter=0;
fi
echo $num
fi


#if [ "$num" -lt "$TARGETTEMPHIGH" && "$num -gt "$TARGETTEMPLOW ]; then
#CURRENTCLOCK=`aticonfig --od-getclocks --adapter=0|grep "Current Clocks"|tr -s ' '|cut -f 5 -d ' '`
#if [ ! "$CURRENTCLOCK" -eq 
#
#
#fi


if [ "$CARD1ENABLE" == "1" ]; then
export DISPLAY=:0.1
num=`aticonfig --pplib-cmd "get temperature 0"|tr -s ' '|cut -f 7 -d ' '|cut -f 1 -d '.'`
if [ "$num" -gt "$OVERHEATTEMP" ]; then
echo "Adapter 1 is overheating clocking it down and increasing fan to 100%"
aticonfig --od-setclocks=$MINCLOCK,$MEMORYCLOCK --adapter=1;
aticonfig --pplib-cmd "set fanspeed 0 75"
echo $num
fi
if [ "$num" -lt "74" ]; then
echo "Clocking up adapter 1"
CURRENTCLOCK=`aticonfig --od-getclocks --adapter=1|grep "Current Clocks"|tr -s ' '|cut -f 5 -d ' '`
if [ ! "$CURRENTCLOCK" -eq "$MAXCLOCK" ]; then
aticonfig --od-setclocks=$MAXCLOCK,$MEMORYCLOCK --adapter=1;
fi
echo $num
fi
fi


if [ "$CARD2ENABLE" == "1" ]; then
export DISPLAY=:0.2
num=`aticonfig --pplib-cmd "get temperature 0"|tr -s ' '|cut -f 7 -d ' '|cut -f 1 -d '.'`
if [ "$num" -gt "$OVERHEATTEMP" ]; then
echo "Adapter 2 is overheating clocking it down and increasing fan to 100%"
aticonfig --od-setclocks=$MINCLOCK,$MEMORYCLOCK --adapter=2;
aticonfig --pplib-cmd "set fanspeed 0 75"
echo $num
fi
if [ "$num" -lt "74" ]; then
echo "Clocking up adapter 2"
CURRENTCLOCK=`aticonfig --od-getclocks --adapter=2|grep "Current Clocks"|tr -s ' '|cut -f 5 -d ' '`
if [ ! "$CURRENTCLOCK" -eq "$MAXCLOCK" ]; then
aticonfig --od-setclocks=$MAXCLOCK,$MAXMEMORY --adapter=2;
#also set fan speed to auto
#aticonfig --pplib-cmd "set fanspeed 0 0"
fi
echo $num
fi
fi


if [ "$CARD3ENABLE" == "1" ]; then
export DISPLAY=:0.3
num=`aticonfig --pplib-cmd "get temperature 0"|tr -s ' '|cut -f 7 -d ' '|cut -f 1 -d '.'`
if [ "$num" -gt "$OVERHEATTEMP" ]; then
echo "Adapter 2 is overheating clocking it down and increasing fan to 100%"
aticonfig --od-setclocks=$MINCLOCK,$MEMORYCLOCK --adapter=3;
aticonfig --pplib-cmd "set fanspeed 0 75"
echo $num
fi
if [ "$num" -lt "74" ]; then
echo "Clocking up adapter 2"
CURRENTCLOCK=`aticonfig --od-getclocks --adapter=3|grep "Current Clocks"|tr -s ' '|cut -f 5 -d ' '`
if [ ! "$CURRENTCLOCK" -eq "$MAXCLOCK" ]; then
aticonfig --od-setclocks=$MAXCLOCK,$MAXMEMORY --adapter=3;
#also set fan speed to auto
#aticonfig --pplib-cmd "set fanspeed 0 0"
fi
echo $num
fi
fi
fi



#DISPLAY=:0 aticonfig --adapter=0 --od-getclocks;
#DISPLAY=:0 aticonfig --adapter=0 --od-getclocks; 
#DISPLAY=:0 aticonfig --od-setclocks=900,1300 --adapter=0;
#DISPLAY=:0 aticonfig --adapter=0 --od-getclocks;
done;
