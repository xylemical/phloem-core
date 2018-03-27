<?php
/**
 * @file
 */

// Ensure something is written to the stdout.
// Also ensure we can read the command line arguments passed.
foreach ($argv as $arg) {
    print "{$arg}\n";
}

// Ensure something is written to the stderr.
file_put_contents( 'php://stderr', "This\nis\nstderr");

exit(1);

