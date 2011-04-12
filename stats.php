<!--<META HTTP-EQUIV=Refresh CONTENT="10">   page refresh -->
<style type="text/css">
body
{
background: #002936;
font: bold italic small-caps 1.5em/2em verdana,sans-serif;
color:gray;
width:100%;
font-size:14px;
line-height: 1.2;
margin-bottom:10em;
}
a, a:visited, a:active {
color: white;
text-decoration: none;
}
a:hover {
color: white;
text-decoration: none;
}
h1{
color:white;
font-size: 1.2em;
letter-spacing:28px;
float:left;
width:90%;
margin: 0 1em  0 1em;	
}
h2
{
letter-spacing:1px;
float:right;

margin: 0 20em 0 0;	
font-size:1em;
}

#header{
color:white;
font-size: 2em;
letter-spacing:5px;
float:left;
margin: 0 1em  0 1em;
}
#contain{
height:100%;
width:100%;
text-align: left;
float:left;
}
#player{
background: #003942;
margin: 0 2em 1em 1em;
color:white;
float:left;
width:26em;
padding:0.5em 0.5em 0 1em;
font-size: 10px;
}
#player .active{background: #007056;width:110%;float:right;margin-bottom:  1em;}
#player .active5{background: #03A780;width:110%;float:right;margin-bottom:  1em;}
#player .active10{background: #38C3FF;width:110%;float:right;margin-bottom:  1em;}
#player .dead{background: #780000;width:110%;float:right;margin-bottom:  1em;}
#player .saved{background: #006878;width:110%;float:right;margin-bottom:  1em;}

#player .name{
float:left;
font-size:1.8em;
width:100%;
height:2.2em;
padding: 0 0 0 .4em;
letter-spacing:3px;
}
#player .status{
float:right;
font-size:1.2em;
margin: -1.5em 0.5em 0 0;
}
#player .stats{
float:left;
font-size:1.4em;
padding:  .2em 0 0 1.2em;
}
#debug
{
color:white;
font-size: 1em;
letter-spacing:5px;
float:left;
margin:1em;
width:1000px;
font: 1.0em/2em verdana,sans-serif;
line-height: 1.5;
}
<!-- margin: top right bottom left -->

</style>
<title>crawl stats</title>
<html><body><div id="contain">

<h1>-------------- crawl stats</h1>

<?php

$debug = false;

// associative array of names
// "username" => "realName" (if wish)
$names = array("chrs" => "Chris", "simonj" => "Matt");

// associative array of one character's stats			  
$statArray = array();

// characters stars are placed in either active, saved, or dead array
$active = array();
$saved = array();
$dead = array();

// loop every name
foreach ($names as $key => $value)
{
	// file read
	$fh = fopen("http://crawl.akrasiac.org/rawdata/{$key}/{$key}.where", 'r');
	$theData = fgets($fh);
	fclose($fh);
	
	// seperate different stats with ':'
	$theData = explode(":", $theData);
	
	// seperate stats key and value with '='
	foreach($theData as $element)
	{
		$e = explode("=", $element);
		if( strlen($e[0]) > 0 && strlen($e[1]) > 0 )		
			$statArray[$e[0]] = $e[1];	
	}
	// trim end of line
	$statArray['status'] = rtrim($statArray['status']);
	
	// place in appropriate array
	if( $statArray['status'] == 'dead' )
		$dead[$key] = $statArray;
	else if( $statArray['status'] == 'active' )
		$active[$key] = $statArray;
	else
		$saved[$key] = $statArray;
	
	// clear array
	$statArray = array();

}

// compair character by xl (level)
function cmp($a, $b)
{
	if( $a['xl'] > $b['xl'] )
		return false;
	return true;
}

// sort each array highest to lowest level
usort($active, "cmp");
usort($saved, "cmp");
usort($dead, "cmp");


// display active
foreach($active as $character )
{
	display($character);
}

// display saved
foreach($saved as $character )
{
	display($character);
}

// display dead
foreach($dead as $character )
{
		display($character);
}

// character display
//    sent associative array of stats
function display($player)
{
	// use "real" name array
	global $names;	

	echo '<div id="player">';	
	
		switch ($player['status'] )
		{
			case 'active':
				if( $player['xl'] >= 10 )
					echo '<div class="active10">';
				else if( $player['xl'] >= 5 )
					echo '<div class="active5">';
				else
					echo '<div class="active">';
			break;
			
			default:
				echo "<div class=\"{$player['status']}\">";
			break;
		}
	
	echo '<br>';
	echo '<div class="name">';
		$link = strtolower($player['name']);
		echo "<a href=\"http://crawl.akrasiac.org/scoring/players/{$link}.html\">";	
		print $names[$player['name']];
		print " the ";
		print($player['title']);
	
		echo "</a>";
	
	echo '</div>';
	
	echo '<div class="status">';
	
		print $player['name'];
		
		print ' (';
		print($player['status']);
		
		if( stristr( $player['v'], "0.9") !== FALSE )
                        print ' on dev';

		print ')';

	echo '</div>';
	

	echo '<div class="stats">';
	
	print($player['race']);
	print " ";
	print($player['cls']);
	
	echo '<br>';  
	print('Level ');
	print($player['xl']);

	print " HP ";
	if($player['status'] != 'dead')
	{
		print($player['hp']);
	}
	else
		print '0';	
	
	print "/";
	print($player['mhp']);

	echo '<br>';	
	print(' Str: ');
	print($player['str']);
	print(' Int: ');
	print($player['int']);
	print(' Dex: ');
	print($player['dex']);	

	echo '<br>';  
	
	if( $player['place'] == "D" )
		print "Dungeon";
	else
		print $player['place'];
		
	echo ' Floor : ';
	print $player['lvl'];
	
	echo '<br>';
	print "Skilled at ";
	print $player['sk'];
	print " (";
	print $player['sklev'];
	print ")";

	/*	
	echo '<br>';
	print $player['goldspent'];
	print " gold spent of ";
	print $player['goldfound'];
	print " found.";	
	*/

	echo '<br><br>';
	
	if($player['god'] != null )
	{
		print('Believer in ');
		print $player['god'];
	}
	else
		print "Atheist";
	
	echo '<br>';
	
	if( $player['status'] == 'dead')
		if($player['god'] == null )
			print "In hell after ";
		else
			print("Died after ");

	print($player['turn']);
	print " turns<br>";
	

	print($player['kills']);
	print " monsters slain";
	
	echo '<br>';echo '<br>';

	
	echo '</div>';
	echo '</div>';
	echo '</div>';
}


if( $debug )
{
	echo '<div id="debug">';
	print "Debuging turned on<br><br>";

	print "<br><br>//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//";
	print "Active Players: ";
	// display active
	foreach($active as $character )
	{
			echo '<pre>';
			print_r ($character);
			echo '</pre>';
	}
	print "//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//";
	print "Saved Players: ";
	// display saved
	foreach($saved as $character )
	{
		echo '<pre>';
		print_r ($character);
		echo '</pre>';
	}
	print "//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//";
	print "Dead Players: ";
	// display dead
	foreach($dead as $character )
	{
		echo '<pre>';
		print_r ($character);
		echo '</pre>';
	}

	echo '</div>';

}

?>
</html>
