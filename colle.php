<?php


function colle (int $x, int $y) : void
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
		//
	};


	$tools = ["interGrid" => $interGrid, "gridContent" => $gridContent];
	//$interGrid($x);
	//$gridContent($x, 3, $grid);
	//var_dump($grid);
	$printGrid($x, $y, $grid, $tools);
	//var_dump($grid);

}


