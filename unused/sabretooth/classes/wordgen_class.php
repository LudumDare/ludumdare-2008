<?php

class wordGenerator {

// Variables

var $alphaB;
var $firstLetter;
var $nextLetter;
var $lastLetter;

// functions


function create($lower_limit=4, $upper_limit=8, $strict=false) {

// Word generation

 $word = "";
 if ($lower_limit < 4)
	$lower_limit = 4;
 if ($upper_limit < 4)
	$upper_limit = 4;
 if ($upper_limit < $lower_limit)
	$upper_limit = $lower_limit;
 $randomLength = mt_rand($lower_limit-2, $upper_limit-2);

 while (strlen($word) < $randomLength) {

	if (strlen($word) == 0)
		$a = $this->choice($this->firstLetter);
	else
		$a = $this->choice($this->nextLetter[strpos($this->alphaB, substr($word, -1))]);

	$word .= $a;

 }

 $a = $this->choice($this->nextLetter[strpos($this->alphaB, substr($word, -1))]);

 $i = strpos($this->alphaB, $a);

 $word .= $a . $this->lastLetter[$i][floor((mt_rand(0,pow(10,5))/pow(10,5))*(mt_rand(0,pow(10,5))/pow(10,5))*count($this->lastLetter[$i]))];

 return ($strict ? substr($word, 0, $upper_limit) : $word);

}


function choice($letters) {

// Choose a letter in a string

 return $letters[floor((mt_rand(0,pow(10,5))/pow(10,5))*(mt_rand(0,pow(10,5))/pow(10,5))*strlen($letters))];

}


function wordGenerator() {

 $this->__construct();
}

function __construct() {
 
 $this->alphaB = "abcdefghijklmnopqrstuvwxyz";
 $this->firstLetter = "toawbcdsfmrhiyeglnpujk";
 $this->nextLetter = array("nnttrrllssidcmvbpyugf", "eloyruias", "ooeeahtiklurc", "eeiitaosbuwhrcfdlnpg", "rrssnnddaattcclleeimwopbfvxgu", "toreaflsmuhcwpbnj", "ehatirouslnmcbwpfg", "eeaaiiotrsu", "nnssttooclremdvgafb", "eoua", "esinaltcowgpmbh", "eealiyodsturmfpcb", "eeaoipmusybfwr", "ttddggeessaaocuwhlybfp", "nnrrffuummtswolpadcvkbi", "eoralituphs", "uuuikm", "eeooaaiistycdmnklrwpubvgh", "tteeooiiaashupwcmbfdnr", "hhiieeooaatrsuwylcb", "sntlcgmpadebf", "eeiaou", "haeionyrst", "ptciaheouwbly", "otaisewcbrpfhdmnlgu", "eeaaoilyz");
 $this->lastLetter = array();
 $this->lastLetter[0]=array("r","s","n","d","nt","m","t","ll","l","ck","it","in","int","ng","x","ys","c");
 $this->lastLetter[1]=array("le","el","our","er","les","ers","out","e");
 $this->lastLetter[2]=array("h","e","i","hes","es","er","her","hant","le","t","tre","les","hers","ree","ts");
 $this->lastLetter[3]=array("e","es","re","er","ent","in","en","ain","ee","it","its");
 $this->lastLetter[4]=array("r","s","n","d","t","nt","rs","nts","ant","nd","a","l");
 $this->lastLetter[5]=array("fle","f","ond");
 $this->lastLetter[6]=array("e","h","ar","le","ht","re","ue","les","out","ues","ing","ion","nant","ard","ast");
 $this->lastLetter[7]=array("","t","er");
 $this->lastLetter[8]=array("t","n","ng","s","l","ls","on","er","e","c","ers","ons");
 $this->lastLetter[9]=array("e","i","a","o","et","er");
 $this->lastLetter[10]=array("","s");
 $this->lastLetter[11]=array("e","s","es","l","eon","er","ant","or","on","t","ist","ets","et","ee","ts","ons","ors","ot","ent","ier","in","ous");
 $this->lastLetter[12]=array("e","ent","es","bre","mes","ain","ore","ents","our","er","ble","p","ant","in","it","ir");
 $this->lastLetter[13]=array("e","t","d","te","ck","tre","es","ant","se","dit","tes","ch","dant","on","ger","s","ces","deer","it","see","tend","tent","ches","ner","ton","dent","nent","al","at","er","nes","fin","ox","ton","oon","ew","ies","son");
 $this->lastLetter[14]=array("us","n","ur","ut","lt","ir","rt","it","nd","nc","rts","m","rs","x","irs","e","w","rd","l","st","ts");
 $this->lastLetter[15]=array("le","e","ers","ee","les","re","ing","ing","ped","ar","es","an","ort","end","on");
 $this->lastLetter[16]=array("ue","ues","uin","uins","uant","uet","uel","uer","uets");
 $this->lastLetter[17]=array("e","t","es","d","ot","and","al","n","as","ct","on","ous","ch","er","ent","st","ands","ant","se","ain","oy","ts","os","ist","mes","ton","iant","nier","der","ment","bes","ber","mee","cher","gest","ick","ix","tant","all","dent","ee","et","ets","gent","mees","od","ail","des","end","le","gy","oid","tic","rax","per","tent","don","ents","ls","ons","onds","ea","an","at");
 $this->lastLetter[18]=array("t","e","ts","ant","es","on","ar","ter","at","er","ant","ent","ons","ters","un","ants","ee","ton","ard","is","ert","ion","ions","ing");
 $this->lastLetter[19]=array("e","er","es","t","ant","ers","ion","ist","ry","our","on","ing","re","its","ent","ons","ar","ast");
 $this->lastLetter[20]=array("s","n","t","m","e","lt","rn","rs","es");
 $this->lastLetter[21]=array("e","er","ant","es","oir","ers","ent","al","ers","rant","as","ax","ient","als","ants");
 $this->lastLetter[22]=array("","","e","on","in");
 $this->lastLetter[23]=array("e","es","a");
 $this->lastLetter[24]=array("","s","z");
 $this->lastLetter[25]=array("e","ar","on","z","ol","al");

}

// End of class
}

?>
