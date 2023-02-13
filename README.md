# UTB-FAI-AAMAI
This PHP code performs multiple runs of three optimization algorithms on three different test functions and outputs the results.

The three optimization algorithms are:
1. randomSearch
2. localSearch
3. stochasticHillClimber

The three test functions are:
1. dejongOne
2. dejongTwo
3. schwefel

The code performs 30 runs of each algorithm on each test function and stores the results in the `$minims` array.

Then, it calls the `printTableResults` function 9 times to print the results of each algorithm on each test function.

The `randomSearch`, `localSearch`, and `stochasticHillClimber` functions perform the optimization on the respective algorithm and the input function, and return an array of fitness values.

The `dejongOne`, `dejongTwo`, and `schwefel` functions are the test functions that the optimization algorithms are applied to.

The `frand` function generates random numbers within a specified range and precision.

The `getRandomNeighbor` function generates a random neighbor within a specified neighborhood and range for the `localSearch` algorithm.
