<?php


function colle (int $x, int $y, array $coords) : void
{
	if ($x == 0 || $y == 0)
		return;

	$grid = [];

	for ($ix = 0; $ix < $x; $ix++) {
		array_push($grid, []);
		for ($iy = 0; $iy < $y; $iy++) {
			array_push($grid[$ix], " ");
		}
	}

	$interGrid = function($x) {
		$counter = 0;
		echo '+';
		for ($i = 0; $i < $x; $i++)
			echo '---+';
		echo PHP_EOL;
	};

	$gridContent = function($x, $y, $grid) {
		echo '|';
		//var_dump($grid);
		for ($i = 0; $i < $x; $i++) {
			//echo $x;
			$cellValue = $grid[$i][$y];
			echo " $cellValue |";
		}
		echo PHP_EOL;
	};

	$printGrid = function($x, $y, $grid, $tools) {
		$tools["interGrid"]($x);
		for ($i = 0; $i < $y; $i++) {
			$tools["gridContent"]($x, $i, $grid);
			$tools["interGrid"]($x);
		}
	};

	$updateGrid = function(&$grid, $x, $y, $newValue) {
		$grid[$x][$y] = $newValue;
	};

	
	$tools = ["interGrid" => $interGrid, "gridContent" => $gridContent];
	//$interGrid($x);
	//$gridContent($x, 3, $grid);
	//var_dump($grid);
	//$printGrid($x, $y, $grid, $tools);

	foreach ($coords as $coord) {
		$tx = $coord[0];
		$ty = $coord[1];
		if ($tx < 0 || $tx > $x || $ty < 0 || $ty > $y){
			continue;
		}
		$updateGrid($grid, $coord[0], $coord[1], 'X');
	}

	// values for forecoming battleship
	// ' ' = empty/missed
	// 1/2 = a ship of player 1/2
	//  X  = wreck of a ship
	$validCoord = function ($str, $x, $y) : bool | array {
		// validity check
		$match = preg_match('/^ {0,}\[ {0,}[0-9]{1,} {0,}, {0,}[0-9]{1,} {0,}\] {0,}$/', $str);
		if (!$match)
			return false;
		$coordsMatch = [];
		preg_match_all('/[0-9]{1,}/', $str, $coordsMatch);
		//var_dump($coordsMatch);
		$mx = intval($coordsMatch[0][0]);
		$my = intval($coordsMatch[0][1]);
		//var_dump($mx, $my);
		if ($mx > $x || $my > $y)
			return false;
		else
			return [$mx, $my];
		//return $match;
		// recovering coords
		// [0-9]{1,}
	};

	$printCoordsInvalid = function () {
		echo "Coords have to be in [x, y] format, use `help` if you need to\n";
	};
	
	//var_dump($validCoord(" [ 1 , 2 ] ", $x, $y));

	$printGrid($x, $y, $grid, $tools);
	while (true) {
		echo "$> ";
		$userEntry = readline();
		//echo "you entered $userEntry\n";
		$argStart = strpos($userEntry, ' ', 0);
		$command = $argStart > 0 ? substr($userEntry, 0, $argStart) : substr($userEntry, 0);
		//var_dump('command', $command, $argStart);
		$coords = $validCoord(substr($userEntry, $argStart), $x, $y);
		switch ($command) {
			// add "help" and "exit" for bonus
			case "query":
				if (!$coords) {
					$printCoordsInvalid();
					break;
				}
				echo $grid[$coords[0]][$coords[1]] != ' ' ? "full\n" : "empty\n";
				break;
			case "add":
				if (!$coords) {
					$printCoordsInvalid();
					break;
				}

				if ($grid[$coords[0]][$coords[1]] != ' ')
					echo "A cross already exists at this location\n";
				else
					$grid[$coords[0]][$coords[1]] = 'X';
				break;
			case "remove":
				if (!$coords) {
					$printCoordsInvalid();
					break;
				}

				if ($grid[$coords[0]][$coords[1]] == ' ')
					echo "No cross exists at this location\n";
				else
					$grid[$coords[0]][$coords[1]] = ' ';
				break;
			case "display":
				$printGrid($x, $y, $grid, $tools);
				break;
			default:
				echo "Unknown command \"$command\"\n";
				break;
		}
	}

	
}


