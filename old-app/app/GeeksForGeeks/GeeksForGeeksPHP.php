<?php
// A PHP program to find maximal
// Bipartite matching.

// This code is contributed by chadan_jnu
// Source: https://www.geeksforgeeks.org/maximum-bipartite-matching/


// M is number of applicants
// and N is number of jobs
$M = 6;
$N = 6;

// A DFS based recursive function
// that returns true if a matching
// for vertex u is possible
function bpm($bpGraph, $u, &$seen, &$matchR)
{
	global $N;

	// Try every job one by one
	for ($v = 0; $v < $N; $v++)
	{
		// If applicant u is interested in
		// job v and v is not visited
		if ($bpGraph[$u][$v] && !$seen[$v])
		{
			// Mark v as visited
			$seen[$v] = true;

			// If job 'v' is not assigned to an
			// applicant OR previously assigned
			// applicant for job v (which is matchR[v])
			// has an alternate job available.
			// Since v is marked as visited in
			// the above line, matchR[v] in the following
			// recursive call will not get job 'v' again
			if ($matchR[$v] < 0 || bpm($bpGraph, $matchR[$v],
									$seen, $matchR))
			{
				$matchR[$v] = $u;
				return true;
			}
		}
	}
	return false;
}

// Returns maximum number
// of matching from M to N
function maxBPM($bpGraph)
{
	global $N,$M;

	// An array to keep track of the
	// applicants assigned to jobs.
	// The value of matchR[i] is the
	// applicant number assigned to job i,
	// the value -1 indicates nobody is
	// assigned.
	$matchR = array_fill(0, $N, -1);

	// Initially all jobs are available

	// Count of jobs assigned to applicants
	$result = 0;
	for ($u = 0; $u < $M; $u++)
	{
		// Mark all jobs as not seen
		// for next applicant.
		$seen=array_fill(0, $N, false);

		// Find if the applicant 'u' can get a job
		if (bpm($bpGraph, $u, $seen, $matchR))
			$result++;
	}
	return $result;
}

// Driver Code

// Let us create a bpGraph
// shown in the above example
$bpGraph = array(array(0, 1, 1, 0, 0, 0),
					array(1, 0, 0, 1, 0, 0),
					array(0, 0, 1, 0, 0, 0),
					array(0, 0, 1, 1, 0, 0),
					array(0, 0, 0, 0, 0, 0),
					array(0, 0, 0, 0, 0, 1));

echo "Maximum number of applicants that can get job is ".maxBPM($bpGraph);


// This code is contributed by chadan_jnu
?>
