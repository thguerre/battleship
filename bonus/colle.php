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

	$gridContent = function($x, $y, $grid, $player) {
		echo '|';
		for ($i = 0; $i < $x; $i++) {
			$cellValue = $grid[$i][$y];
			if (is_int($cellValue))
			       if ($cellValue != $player)
					$cellValue = ' ';
				else
					$cellValue = $cellValue+1;
			echo " $cellValue |";
		}
		echo PHP_EOL;
	};

	$printGrid = function($x, $y, $grid, $tools, $player) {
		$tools["interGrid"]($x);
		for ($i = 0; $i < $y; $i++) {
			$tools["gridContent"]($x, $i, $grid, $player);
			$tools["interGrid"]($x);
		}
	};

	$updateGrid = function(&$grid, $x, $y, $newValue) {
		$grid[$x][$y] = $newValue;
	};

	
	$tools = ["interGrid" => $interGrid, "gridContent" => $gridContent];

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
		$mx = intval($coordsMatch[0][0]);
		$my = intval($coordsMatch[0][1]);
		if ($mx > $x || $my > $y)
			return false;
		else
			return [$mx, $my];
	};

	$printCoordsInvalid = function () {
		echo "Coords have to be in [x, y] format, use `help` if you need to\n";
	};
	

	$printGrid($x, $y, $grid, $tools, 0);


	// game initialization
	
	$playerCount = 2;
	$playerMaxShips = 2;
	$playerShipCount = array_fill(0, $playerCount, 0);


	$currentPlayer = 0;
	echo "Player 1, place your $playerMaxShips ships :\n";

	$winner = false;
	$shipsPlaced = false;
	$cheated = false;

	while (!$winner) {
		if ($shipsPlaced)
			echo "Player ".($currentPlayer+1).", launch your attack :\n";
		echo "Player ".($currentPlayer+1)." $> ";

		$userEntry = readline();
		$argStart = strpos($userEntry, ' ', 0);
		$command = $argStart > 0 ? substr($userEntry, 0, $argStart) : substr($userEntry, 0);
		$coords = $validCoord(substr($userEntry, $argStart), $x, $y);
		switch ($command) {
			case "query":
				if (!$shipsPlaced) {
					echo "All ships havn't been placed yet !\n";
					break;
				}
				if (!$coords) {
					$printCoordsInvalid();
					break;
				}
				$cellVal = $grid[$coords[0]][$coords[1]];
			       	$cellEmpty = $cellVal == ' ';
				if ($cellEmpty || $cellVal == $currentPlayer || $cellVal == 'X') {
					echo "Player ".($currentPlayer+1).", ";
					switch ($cellVal) {
						case $currentPlayer:
							echo "you nearly shot at friendly ship !\n";
							break;
						case 'X':
							echo "that ship has already been wrecked \n";
							break;
						default:
							echo "you didn't touch anything.\n";
							break;
					}
				} else {
					$playerShipCount[$cellVal]--;
					$grid[$coords[0]][$coords[1]] = 'X';
					echo "Player ".($currentPlayer+1).", you touched a boat of player ".($cellVal+1)." !\n";

					if ($playerShipCount[$cellVal] == 0) {
						$winner = $currentPlayer;
						break 2;
					}
				}
				$currentPlayer = $currentPlayer+1 == $playerCount ? 0 : $currentPlayer +1;
				
				break;
			case "add":
				if ($shipsPlaced) {
					echo "All ships already have been placed !\n";
					break;
				}
				if (!$coords) {
					$printCoordsInvalid();
					break;
				}

				if ($grid[$coords[0]][$coords[1]] != ' ')
					echo "A cross already exists at this location\n";
				else {
					$grid[$coords[0]][$coords[1]] = $currentPlayer;
					$playerShipCount[$currentPlayer]++;
					if ($playerShipCount[$currentPlayer] == $playerMaxShips) {
						$currentPlayer++;
						if ($currentPlayer == $playerCount){
							$currentPlayer = 0;
							$shipsPlaced = true;
						} else {
							$tmp = $currentPlayer+1;
							echo "Player $tmp, place your $playerMaxShips ships :\n";
						}
					}
				}
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
				$cheated = true;
				$printGrid($x, $y, $grid, $tools, $currentPlayer);
				break;
			case "exit":
				exit();
				break;
			case "help":
				echo "Battleship commands:";
			default:
				echo "Unknown command \"$command\"\n";
				break;
		}
	}
	
	echo "Player ".($winner+1)." won !!\n";
}

colle(4, 4, []);
