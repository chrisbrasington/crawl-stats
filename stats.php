<META HTTP-EQUIV=Refresh CONTENT="10">   <!-- page refresh -->
<title>crawl stats</title>
<html>
<head>
<link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body><div id="contain">

<h1>-------------- crawl stats</h1>

<?php

$debug = true;

// associative array of names
// "username" => "realName" (if wish)
$names = array("chrs" => "Chris", "Eclisiast" => "Geddy", "Malyzapan" => "Anthony",
                          "Dragon" => "Tyler", "pgs2a" => "Gray",
                          "simonj" => "Matt", "Speranza" => "Kevin",
                          "yumari" => "Joseph", "saiydan21" => "Daniel",
                          "domo" => "Dom");


// associative array of one character's stats			  
$statArray = array();

// characters stars are placed in either active, saved, or dead array
$active = array();
$saved = array();
$dead = array();
$won = array();

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
usort($won, "cmp");

// WON!
foreach($won as $character )
{
	display($character);
}

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
			case 'won':
				echo '<div class="won">';
			break;
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
		
		if( $player['status'] == "won" )
			print " has WON THE GAME!";
		else
		{
			print " the ";
			print($player['title']);
		}

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
	
	if( $player['place'] == "Trove" )
	{
		print "Looting the Treasure Trove";
	}
	else
	{
		if( $player['place'] == "D" )
			print "Dungeon";
		else
			print $player['place'];
		
		echo ' Floor : ';
		print $player['lvl'];
	}

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
</body>
</html>
