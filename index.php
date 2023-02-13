<!DOCTYPE html>
<html>
<head>
    <title>AAMAI</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>

<?php

// to use in the graph
$minims = [];
foreach(range(1, 30) as $r) {
    $minims['randomSearch']['dejongOne'][] = randomSearch('dejongOne');
    $minims['randomSearch']['dejongTwo'][] = randomSearch('dejongTwo');
    $minims['randomSearch']['schwefel'][] = randomSearch('schwefel');

    $minims['localSearch']['dejongOne'][] = localSearch('dejongOne');
    $minims['localSearch']['dejongTwo'][] = localSearch('dejongTwo');
    $minims['localSearch']['schwefel'][] = localSearch('schwefel');

    $minims['stochasticHillClimber']['dejongOne'][] = stochasticHillClimber('dejongOne');
    $minims['stochasticHillClimber']['dejongTwo'][] = stochasticHillClimber('dejongTwo');
    $minims['stochasticHillClimber']['schwefel'][] = stochasticHillClimber('schwefel');
}

printTableResults($minims, 'randomSearch', 'dejongOne');
printTableResults($minims, 'randomSearch', 'dejongTwo');
printTableResults($minims, 'randomSearch', 'schwefel');
printTableResults($minims, 'localSearch', 'dejongOne');
printTableResults($minims, 'localSearch', 'dejongTwo');
printTableResults($minims, 'localSearch', 'schwefel');
printTableResults($minims, 'stochasticHillClimber', 'dejongOne');
printTableResults($minims, 'stochasticHillClimber', 'dejongTwo');
printTableResults($minims, 'stochasticHillClimber', 'schwefel');

// ALGOS

function randomSearch($function): array {
    $minims = [];
    $maxIterations = 1000;
    $fitnessStart = 100000;
    foreach(range(1, $maxIterations) as $i) {
        if($function === 'dejongOne') {
            $x1 = frand(-5, 5, 9);
            $x2 = frand(-5, 5, 9);
            $fitness = dejongOne($x1, $x2);
        } elseif($function === 'dejongTwo') {
            $x1 = frand(-5, 5, 9);
            $x2 = frand(-5, 5, 9);
            $fitness = dejongTwo($x1, $x2);
        } elseif($function === 'schwefel') {
            $x1 = frand(-500, 500, 9);
            $x2 = frand(-500, 500, 9);
            $fitness = schwefel($x1, $x2);
        } else throw new Exception('Unknown function');

        if($fitness < $fitnessStart) {
            $fitnessStart = $fitness;
        }
        array_push($minims, $fitnessStart);
    }
    return $minims;
}

function localSearch($function): array {
    if(in_array($function, ['dejongOne', 'dejongTwo'])) {
        $minArg = -5;
        $maxArg = 5;
        $neighborhood = 0.5;
    } elseif($function === 'schwefel') {
        $minArg = -500;
        $maxArg = 500;
        $neighborhood = 5;
    } else throw new Exception('Unknown function');

    $minims = [];
    $neighborhoodSize = 10;
    $maxFes = 1000/$neighborhoodSize;

    $xStar1 = frand($minArg, $maxArg, 9);
    $xStar2 = frand($minArg, $maxArg, 9);
    $bestSolution = $function($xStar1, $xStar2);
    $i = 0;
    do {
        if($i >= $maxFes) break;

        $currentNeighborhood = [];

        // generating neighborhood
        foreach(range(1, $neighborhoodSize) as $neighborhoodIteration) {
            $nx1 = getRandomNeighbor($xStar1, $neighborhood, $minArg, $maxArg);
            $nx2 = getRandomNeighbor($xStar2, $neighborhood, $minArg, $maxArg);
            array_push($currentNeighborhood, [$nx1, $nx2]);
        }

        $bestPoint = [];
        $bestLocalSolution = $bestSolution;
        foreach($currentNeighborhood as $point) {
            $localSolution = $function($point[0], $point[1]);
            if($localSolution < $bestLocalSolution) {
                $bestLocalSolution = $localSolution;
                $bestPoint = [$point[0], $point[1]];
            }
        }

        $i++;
        array_push($minims, $bestLocalSolution);
        if($bestLocalSolution < $bestSolution) {
            $xStar1 = $bestPoint[0];
            $xStar2 = $bestPoint[1];
            $bestSolution = $bestLocalSolution;
        }
    } while(true);
    return $minims;
}

function stochasticHillClimber($function): array {
    if(in_array($function, ['dejongOne', 'dejongTwo'])) {
        $minArg = -5;
        $maxArg = 5;
        $neighborhood = 0.5;
    } elseif($function === 'schwefel') {
        $minArg = -500;
        $maxArg = 500;
        $neighborhood = 5;
    } else throw new Exception('Unknown function');

    $minims = [];
    $t = 1;
    $neighborhoodSize = 10;
    $tmax = 1000/$neighborhoodSize;
    $xStar1 = frand($minArg, $maxArg, 9);
    $xStar2 = frand($minArg, $maxArg, 9);
    $bestSolution = $function($xStar1, $xStar2);

    while($t <= $tmax) {
        $currentNeighborhood = [];

        // generating neighborhood
        foreach(range(1, $neighborhoodSize) as $neighborhoodIteration) {
            $nx1 = getRandomNeighbor($xStar1, $neighborhood, $minArg, $maxArg);
            $nx2 = getRandomNeighbor($xStar2, $neighborhood, $minArg, $maxArg);
            array_push($currentNeighborhood, [$nx1, $nx2]);
        }

        $bestPoint = [];
        $bestLocalSolution = $bestSolution;
        foreach($currentNeighborhood as $point) {
            $localSolution = $function($point[0], $point[1]);
            if($localSolution < $bestLocalSolution) {
                $bestLocalSolution = $localSolution;
                $bestPoint = [$point[0], $point[1]];
            }
        }

        if($bestLocalSolution < $bestSolution) {
            $bestSolution = $bestLocalSolution;
            $xStar1 = $bestPoint[0];
            $xStar2 = $bestPoint[1];
        }
        array_push($minims, $bestSolution);
        $t++;
    }
    return $minims;
}

// TEST FUNCTIONS

function dejongOne($x1, $x2): float {
    return pow($x1, 2)+pow($x2, 2);
}

function dejongTwo($x1, $x2): float {
    $result = 100.0;
    $result *= pow(pow($x1, 2) - $x2, 2);
    $result += pow((1 - $x1), 2);
    return $result;
}

function schwefel($x1, $x2): float {
    return (-$x1*sin(sqrt(abs($x1)))) + (-$x2*sin(sqrt(abs($x2))));
}

// ALGO FUNCTIONS

function getRandomNeighbor($x1, $neighborhood, $min, $max): float {
    $neighbor = frand($x1 - $neighborhood, $x1 + $neighborhood, 9);
    if($neighbor >= $min && $neighbor <= $max) {
        return $neighbor;
    } else {
        return getRandomNeighbor($x1, $neighborhood, $min, $max);
    }
}

// AUX

function writeLine($string): void {
    echo $string . "<br>";
}

function printTableResults($minims, $wantedAlgorithm = '', $wantedFunction = ''): void {
    foreach($minims as $algorithmName => $functionResults) {
        if($wantedAlgorithm && $wantedAlgorithm !== $algorithmName) continue;
        echo '<h2>' . $algorithmName . '</h2>';
        foreach($functionResults as $functionName => $runs) {

            if($wantedFunction && $wantedFunction !== $functionName) continue;

            echo '<h3>' . $functionName . '</h3>';
            echo '<table class="table table-bordered">';
            echo '<thead><tr><th></th>';
            foreach(range(1, count($runs)) as $r) {
                echo '<th>' . $r . '</th>';
            }
            echo '</tr></thead>';

            foreach(range(0, count($runs[0])-1) as $row) {
                foreach(range(0, count($runs)-1) as $column) {
                    if($column === 0) {
                        echo '<tr>';
                        echo '<td>' . ($row+1) . '</td>';
                    }

                    echo '<td>' . $runs[$column][$row] . '</td>';

                    if($column === (count($runs)-1)) {
                        echo '</tr>';
                    }
                }
            }

            echo '</table>';
        }
    }
}

function frand($min, $max, $decimals = 0) {
    $scale = pow(10, $decimals);
    return mt_rand($min * $scale, $max * $scale) / $scale;
}

?>
</body>
</html>
