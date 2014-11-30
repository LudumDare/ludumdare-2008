<?php
// ?admin=1 will show results before the voting is closed for admin level users

function _compo_vote($m) {
    list($state,$opts) = explode(":",html_entity_decode($m[1]));
    $opts = explode(";",$opts);
    natcasesort($opts);
    $pid = intval($GLOBALS["post"]->ID);
    ob_start();
    
    $user = wp_get_current_user();
    if ($user->user_level >= 10) {
        if (isset($_REQUEST["admin"])) {
            $state = "closed";
        }
    }

    if ($state == "open") { _compo_vote_do($pid,$opts); }
    if ($state == "open") { _compo_vote_form($pid,$opts); }
    if ($state == "closed") { _compo_vote_results($pid); }
    $r = ob_get_contents();
    ob_end_clean();
    return $r;
}

function compo_vote_google($name) {
    $link = "http://www.google.com/search?q=".urlencode($name);
    return "<a href=\"$link\" target='_blank'>".htmlentities($name)."</a>";
}


function _compo_vote_results($pid) {
    // CACHE ///////////////////////////////////////////////////////////////
    if (($cres=compo2_cache_read(0,$ckey="compo_vote_results:$pid",5*60))!==false) {
        if (!isset($_REQUEST["admin"])) { echo $cres; return; }
    }
    ob_start();
    ////////////////////////////////////////////////////////////////////////

    global $compo;
/*    
    $r = compo_query("select * from {$compo['vote.table']} where pid = ? and uid = ? order by value desc",array($pid,0));
    
    $r2 = compo_query("select count(*) c, name,value , concat(name,'|',value) as n_v from {$compo['vote.table']} where pid = ? and uid != 0 group by n_v",array($pid));
    $data = array(); foreach ($r2 as $e) { $data[$e["name"]][$e["value"]] = $e["c"]; }
*/

	$fields_query = compo_query("SELECT DISTINCT name FROM {$compo['vote.table']} WHERE pid = ?",array($pid));
	$fields = [];
	foreach( $fields_query as $field ) {
		$fields[] = $field['name'];
	}
	
	$data = [];
	
	foreach( $fields as $field ) {
		$data[] = array_pop(compo_query("
			SELECT name,
				SUM(value) AS result,
				SUM(CASE WHEN value = -1 then 1 end) as downvote,
				SUM(CASE WHEN value = 0 then 1 end) as novote,
				SUM(CASE WHEN value = 1 then 1 end) as upvote,
				COUNT(value) AS total
			FROM {$compo['vote.table']} 
			WHERE pid = ? AND uid != 0 AND name = ?",
			array($pid,$field)));
	}
	
	usort($data,function($a,$b){
		return $b['result'] - $a['result'];
	});
	
	//print_r( $data );

    echo "<table>";
    echo "<tr><th><th><th><th align=center>+1<th align=center>0<th align=center>-1<th align=center>Total Votes";
    $n=1;
    foreach ($data as $e) {
        echo "<tr>";
        echo "<th>{$n}.";$n++;
        echo "<td>".compo_vote_google($e['name']);
        echo "<th>".htmlentities($e['result'])."";
        echo "<td>".$e['upvote'];
        echo "<td>".$e['novote'];
        echo "<td>".$e['downvote'];
        echo "<td>".$e['total'];
    }
    echo "</table>";

//    $e = array_pop(compo_query("
//    	SELECT sum(value) as v
//    	FROM {$compo['vote.table']}
//    	WHERE pid = ? AND uid != 0",// AND name = ?",
//    	array($pid,$name)));

/*   
    echo "<table>";
    echo "<tr><th><th><th><th align=center>+1<th align=center>0<th align=center>-1";
    $n=1;
    foreach ($r as $e) {
        echo "<tr>";
        echo "<th>{$n}.";$n++;
        echo "<td>".compo_vote_google($e["name"]);
        $v = $e["value"];
        if ($v>0) { $v="+$v"; }
//         echo "<th>(".htmlentities($v).")";
        echo "<th>".htmlentities($v)."";
        echo "<td>".$data[$e["name"]]["1"];
        echo "<td>".$data[$e["name"]]["0"];
        echo "<td>".$data[$e["name"]]["-1"];
    }
    echo "</table>";
*/ 
    // CACHE ///////////////////////////////////////////////////////////////
    $cres = ob_get_contents();
    ob_end_clean();
    compo2_cache_write(0,$ckey,$cres);
    echo $cres;
    ////////////////////////////////////////////////////////////////////////
}

/*
// OLD VERSION //
function _compo_vote_do($pid,$opts) {
    global $compo;
    $cur = wp_get_current_user();
    $uid = $cur->ID;
    if (!$uid) { return; }
    
    $action = isset($_REQUEST["compo_vote_action"])?$_REQUEST["compo_vote_action"]:"";
    if ($action != "") {
        compo_query("delete from {$compo["vote.table"]} where pid = ? and uid = ?",array($pid,$uid));
        foreach ($opts as $k=>$name) {
            $key = "vote_{$k}";
            $v = (strlen($_REQUEST[$key])?max(-1,min(1,intval($_REQUEST[$key]))):"");
            if (strlen($v)) {
                compo_query("insert into {$compo["vote.table"]} (pid,uid,name,value) values (?,?,?,?)",array($pid,$uid,$name,$v));
            }
            compo_query("delete from {$compo["vote.table"]} where pid = ? and uid = ? and name = ?",array($pid,0,$name));
            $e = array_pop(compo_query("select sum(value) as v from {$compo["vote.table"]} where pid = ? and uid != 0 and name = ?",array($pid,$name)));
            compo_query("insert into {$compo["vote.table"]} (pid,uid,name,value) values (?,?,?,?)",array($pid,0,$name,$e["v"]));
        }
        echo "<p>Thanks for voting!</p>";
    }
}
*/

// MK Version //
function _compo_vote_do($pid,$opts) {
    global $compo;
    $cur = wp_get_current_user();
    $uid = $cur->ID;
    if (!$uid) { return; }
    
    $action = isset($_REQUEST["compo_vote_action"])?$_REQUEST["compo_vote_action"]:"";
    if ($action != "") {
//        compo_query("delete from {$compo["vote.table"]} where pid = ? and uid = ?",array($pid,$uid));
        foreach ($opts as $k=>$name) {
            $key = "vote_{$k}";
            $v = (strlen($_REQUEST[$key])?max(-1,min(1,intval($_REQUEST[$key]))):"");
            if (strlen($v)) {
                compo_query("
                	INSERT INTO {$compo['vote.table']} (
                		pid,
                		uid,
                		name,
                		
                		value
                	) 
                	VALUES (?,?,?,?)
                	ON DUPLICATE KEY UPDATE
                		value=VALUES(value)",
                	array($pid,$uid,$name,$v));
            }
//            compo_query("delete from {$compo["vote.table"]} where pid = ? and uid = ? and name = ?",array($pid,0,$name));
//            $e = array_pop(compo_query("select sum(value) as v from {$compo["vote.table"]} where pid = ? and uid != 0 and name = ?",array($pid,$name)));
//            compo_query("insert into {$compo["vote.table"]} (pid,uid,name,value) values (?,?,?,?)",array($pid,0,$name,$e["v"]));
        }
        echo "<p>Thanks for voting!</p>";
    }
}


function _compo_vote_form($pid,$opts) {
    global $compo;
    $cur = wp_get_current_user();
    $uid = $cur->ID;
    if (!$uid) { echo "<p>You must sign in to vote.</p>"; return; }
    
    $data = compo_query("select * from {$compo['vote.table']} where pid = ? and uid = ?",array($pid,$uid));
    $r = array(); foreach ($data as $e) { $r[$e["name"]] = $e["value"]; }

    echo "<style>.s,.us { cursor:pointer; } .us { opacity: 0.15; -moz-opacity: 0.15; filter: alpha(opacity=15); }</style>";
//     echo "<p>$pid $uid</p>";

    $topurl = get_bloginfo("url");
    echo "<script type='text/javascript' src='$topurl/wp-content/plugins/compo/vote.js'></script>";

    echo "<form class='vote' method='post'>";
    //echo "<input type='submit' value='Vote!'>";
    
    echo "<input type='hidden' name='compo_vote_action' value=1>";
    echo "<table class='table'>";
    foreach ($opts as $k=>$name) {
        $key = "vote_{$k}";
        $v = $r[$name];
        echo "<tr>";
        echo "<td><nobr>";compo_vote_fakeajax($key,$v);echo "</nobr>";
        echo "<td class='{$key}' align=left>".compo_vote_google($name);
    }
    echo "</table>";
    
//     $total = count($opts);
//     echo "<script type='text/javascript'>initvote($total);</script>";
	echo "<br />";
    echo "<input class='button' type='submit' value='Vote!'>";
    echo "</form>";
}

function compo_vote_fakeajax($k,$v) {
    $topurl = get_bloginfo("url");
    echo "<input name='$k' id='$k' value='$v' onChange='view(\"$k\")' type='hidden'/>";
    $c = (strcmp($v,"1")==0?"s":"us");
    echo "<img src='$topurl/wp-content/plugins/compo/images/thumbsup.gif' id='1$k' alt='+1' class='$c' onClick='set(\"$k\",1)' />";
    $c = (strcmp($v,"0")==0?"s":"us");
    echo "<img src='$topurl/wp-content/plugins/compo/images/undecided.gif' id='0$k' alt='0' class='$c' onClick='set(\"$k\",0)' />";
    $c = (strcmp($v,"-1")==0?"s":"us");
    echo "<img src='$topurl/wp-content/plugins/compo/images/thumbsdown.gif' id='-1$k' alt='-1' class='$c' onClick='set(\"$k\",-1)' />";
}



function compo_vote($content) {
    $content = preg_replace_callback("/\[compo\-vote\:(.*?)\]/","_compo_vote",$content);
    return $content;
}

?>