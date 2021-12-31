<?php

/**
 * This function calculate difference of an end and start time
 *
 * @param string  $startTime    Start time 
 * @param string  $endTime      End time 
 * 
 * @author Majid Abbasi <majid.abbasi.56@gmail.com>
 * @return Minute
 */ 
function getDurationTimeInMinute($startTime, $endTime){
   return (strtotime($endTime) - strtotime($startTime)) / 60;
}