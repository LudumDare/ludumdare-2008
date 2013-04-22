/*******************************************************************************\
Countdown Timer JavaScript Module
Version 3.0.3 (kept in step with fergcorp_countdownTimer.php)
Copyright (c) 2007-2012 Andrew Ferguson
---------------------------------------------------------------------------------
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
\*******************************************************************************/

function rtrim(stringToTrim) {
	return stringToTrim.replace(/..$/,"");
}

function _n(singular, plural, count){
	if(count == 1){
		return singular;
	}
	else{
		return plural;
	}
}

//http://stackoverflow.com/questions/7583549/string-as-object-reference-to-object-variable
function GetPropertyByString(stringRepresentation) {
    var properties = stringRepresentation.split("."),
        myTempObject = window[properties[0]];
    for (var i = 1, length = properties.length; i<length; i++) {
    myTempObject = myTempObject[properties[i]];
    }

    return myTempObject;
}

function fergcorp_countdownTimer_js ()
{
		jQuery("abbr.fergcorp_countdownTimer_event_time").each(function (i) {
			var nowDate = new Date();
			var targetDate = new Date(GetPropertyByString("fergcorp_countdown_timer_jsEvents." + this.id)*1000);
			
			if((targetDate - nowDate) < 0){
				this.innerHTML = sprintf(fergcorp_countdown_timer_js_lang.agotime, fergcorp_countdownTimer_fuzzyDate(nowDate, targetDate, fergcorp_countdown_timer_options));			
			}
			else if((targetDate - nowDate) >= 0 ){
				this.innerHTML = sprintf(fergcorp_countdown_timer_js_lang.intime, fergcorp_countdownTimer_fuzzyDate(targetDate, nowDate, fergcorp_countdown_timer_options));
			}
			
		});
   
    window.setTimeout('fergcorp_countdownTimer_js()', 1000);
}

function fergcorp_countdownTimer_fuzzyDate(targetTime, nowTime, getOptions){
	var rollover = 0;
	var sigNumHit = false;
	var totalTime = 0;

	var nowDate = nowTime;
	var targetDate = targetTime;
	
	var s = '';
	
	var nowYear = nowDate.getUTCFullYear();
	var nowMonth = nowDate.getUTCMonth() + 1;
	var nowDay = nowDate.getUTCDate();
	var nowHour = nowDate.getUTCHours();
	var nowMinute = nowDate.getUTCMinutes();
	var nowSecond = nowDate.getUTCSeconds();
	
	var targetYear = targetDate.getUTCFullYear();
	var targetMonth = targetDate.getUTCMonth() + 1;
	var targetDay = targetDate.getUTCDate();
	var targetHour = targetDate.getUTCHours();
	var targetMinute = targetDate.getUTCMinutes();
	var targetSecond = targetDate.getUTCSeconds();
	
	var resultantYear = targetYear - nowYear;
	var resultantMonth = targetMonth - nowMonth;
	var resultantDay = targetDay - nowDay;
	var resultantHour = targetHour - nowHour;
	var resultantMinute = targetMinute - nowMinute;
	var resultantSecond = targetSecond - nowSecond;

	if(resultantSecond < 0){
		resultantMinute--;
		resultantSecond = 60 + resultantSecond;
	}
	
	if(resultantMinute < 0){
		resultantHour--;
		resultantMinute = 60 + resultantMinute;
	}
	
	if(resultantHour < 0){
		resultantDay--;
		resultantHour = 24 + resultantHour;
	}
	
	if(resultantDay < 0){
		resultantMonth--;
		resultantDay = resultantDay + 32 - new Date(nowYear, nowMonth-1, 32).getUTCDate();
	}
	
	

	if(resultantMonth < 0){
		resultantYear--;
		resultantMonth = resultantMonth + 12;
	}

	//Year
	if(parseInt( getOptions['showYear'] )){
		if(sigNumHit || !parseInt( getOptions['stripZero'] ) || resultantYear){
			s = '<span class="fergcorp_countdownTimer_year fergcorp_countdownTimer_timeUnit">' + sprintf(_n(fergcorp_countdown_timer_js_lang.year, fergcorp_countdown_timer_js_lang.years, resultantYear), resultantYear) + '</span> ';
			sigNumHit = true;
		}
	}
	else{
		rollover = resultantYear*31536000;
	}

	//Month	
	if(parseInt( getOptions['showMonth'] )){
		if(sigNumHit || !parseInt( getOptions['stripZero'] ) || (resultantMonth + parseInt(rollover/2628000)) ){
			resultantMonth = resultantMonth + parseInt(rollover/2628000);
			s = s + '<span class="fergcorp_countdownTimer_month fergcorp_countdownTimer_timeUnit">' + sprintf(_n(fergcorp_countdown_timer_js_lang.month, fergcorp_countdown_timer_js_lang.months, resultantMonth), resultantMonth) + '</span> ';
			rollover = rollover - parseInt(rollover/2628000)*2628000;
			sigNumHit = true;
		}
	}
	else{
		//If we don't want to show months, let's just calculate the exact number of seconds left since all other units of time are fixed (i.e. months are not a fixed unit of time)		
		totalTime = parseInt(targetTime.getTime() - nowTime.getTime())/1000;
		
		//If we showed years, but not months, we need to account for those.
		if(parseInt( getOptions['showYear'] )){
			totalTime = totalTime - resultantYear*31536000;
		}
			
		//Re calculate the resultant times
		resultantWeek = 0;//parseInt( totalTime/(86400*7) );
 
		resultantDay = parseInt( totalTime/86400 );

		resultantHour = parseInt( (totalTime - resultantDay*86400)/3600 );
		
		resultantMinute = parseInt( (totalTime - resultantDay*86400 - resultantHour*3600)/60 );
		
		resultantSecond = parseInt( (totalTime - resultantDay*86400 - resultantHour*3600 - resultantMinute*60) );
		
		//and clear any rollover time
		rollover = 0;

	}
	
	//Week (weeks are counted differently becuase we can just take 7 days and call it a week...so we do that)
	if(parseInt( getOptions['showWeek'] )){
		if(sigNumHit || !parseInt( getOptions['stripZero'] ) || parseInt( (resultantDay + parseInt(rollover/86400) )/7)){
			resultantDay = resultantDay + parseInt(rollover/86400);
			s = s + '<span class="fergcorp_countdownTimer_week fergcorp_countdownTimer_timeUnit">' + sprintf(_n(fergcorp_countdown_timer_js_lang.week, fergcorp_countdown_timer_js_lang.weeks, (parseInt( (resultantDay + parseInt(rollover/86400) )/7))), (parseInt( (resultantDay + parseInt(rollover/86400) )/7))) + '</span> ';
			rollover = rollover - parseInt(rollover/86400)*86400;
			resultantDay = resultantDay - parseInt( (resultantDay + parseInt(rollover/86400) )/7 )*7;
			sigNumHit = true;
		}
	}

	//Day
	if(parseInt( getOptions['showDay'] )){
		if(sigNumHit || !parseInt( getOptions['stripZero'] ) || (resultantDay + parseInt(rollover/86400)) ){
			resultantDay = resultantDay + parseInt(rollover/86400);
			s = s + '<span class="fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit">' + sprintf(_n(fergcorp_countdown_timer_js_lang.day, fergcorp_countdown_timer_js_lang.days, resultantDay), resultantDay) + '</span> ';
			rollover = rollover - parseInt(rollover/86400)*86400;
			sigNumHit = true;
		}
	}
	else{
		rollover = rollover + resultantDay*86400;
	}
	
	//Hour
	if(parseInt( getOptions['showHour'] )){
		if(sigNumHit || !parseInt( getOptions['stripZero'] ) || (resultantHour + parseInt(rollover/3600)) ){
			resultantHour = resultantHour + parseInt(rollover/3600);
			s = s + '<span class="fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit">' + sprintf(_n(fergcorp_countdown_timer_js_lang.hour, fergcorp_countdown_timer_js_lang.hours, resultantHour), resultantHour) + '<span> ';
			rollover = rollover - parseInt(rollover/3600)*3600;
			sigNumHit = true;
		}
	}
	else{
		rollover = rollover + resultantHour*3600;
	}
	
	//Minute
	if(parseInt( getOptions['showMinute'] )){
		if(sigNumHit || !parseInt( getOptions['stripZero'] ) || (resultantMinute + parseInt(rollover/60)) ){
			resultantMinute = resultantMinute + parseInt(rollover/60);
			s = s + '<span class="fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit">' + sprintf(_n(fergcorp_countdown_timer_js_lang.minute, fergcorp_countdown_timer_js_lang.minutes, resultantMinute), resultantMinute) + '</span> ';
			rollover = rollover - parseInt(rollover/60)*60;
			sigNumHit = true;
		}
	}
	else{
		rollover = rollover + resultantMinute*60;
	}
	
	//Second
	if(parseInt( getOptions['showSecond'] )) {
		resultantSecond = resultantSecond + rollover;
		s = s + '<span class="fergcorp_countdownTimer_second fergcorp_countdownTimer_timeUnit">' + sprintf(_n(fergcorp_countdown_timer_js_lang.second, fergcorp_countdown_timer_js_lang.seconds, resultantSecond), resultantSecond) + '</span> ';
	}
	
	
	//Catch blank statements
	if(s==''){
		if(parseInt( getOptions['showSecond'] )){
			s = '<span class="fergcorp_countdownTimer_second fergcorp_countdownTimer_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.seconds, 0) + '</span> ';
		}
		else if(parseInt( getOptions['showMinute'] )){
			s = '<span class="fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.minutes, 0) + '</span> ';
		}
		else if(parseInt( getOptions['showHour'] )){
			s = '<span class="fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.hours, 0) + '</span> ';
		}	
		else if(parseInt( getOptions['showDay'] )){
			s = '<span class="fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.days, 0) + '</span> ';
		}
		else if(parseInt( getOptions['showWeek'] )){
			s = '<span class="fergcorp_countdownTimer_week fergcorp_countdownTimer_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.weeks, 0) + '</span> ';
		}
		else if(parseInt( getOptions['showMonth'] )){
			s = '<span class="fergcorp_countdownTimer_month fergcorp_countdownTimer_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.months, 0) + '</span> ';
		}
		else{
			s = '<span class="fergcorp_countdownTimer_year fergcorp_countdownTimer_timeUnit">' + sprintf(fergcorp_countdown_timer_js_lang.years, 0) + '</span> ';
		}
	}

	
	return s.replace(/(, ?<\/span> *)$/, "<\/span>"); //...and return the result (a string)
}
fergcorp_countdownTimer_js();