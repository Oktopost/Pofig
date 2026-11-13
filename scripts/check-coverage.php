<?php
/**
 * Fails when the line coverage in a PHPUnit clover report is below a threshold.
 *
 * Usage: php scripts/check-coverage.php <clover.xml> <minimum line coverage percent>
 */

if ($argc !== 3 || !is_numeric($argv[2]))
{
	fwrite(STDERR, "Usage: php {$argv[0]} <clover.xml> <minimum line coverage percent>\n");
	exit(1);
}

[, $file, $required] = $argv;
$required = (float)$required;

if (!is_file($file) || !is_readable($file))
{
	fwrite(STDERR, "Coverage report $file not found. Run phpunit with --coverage-clover $file first.\n");
	exit(1);
}

$xml = @simplexml_load_file($file);
$metrics = $xml ? $xml->project->metrics : null;

if (!$metrics || !isset($metrics['statements'], $metrics['coveredstatements']))
{
	fwrite(STDERR, "Coverage report $file has no project metrics.\n");
	exit(1);
}

$statements = (int)$metrics['statements'];
$covered = (int)$metrics['coveredstatements'];

if ($statements === 0)
{
	fwrite(STDERR, "Coverage report $file has no statements.\n");
	exit(1);
}

$actual = $covered * 100 / $statements;

printf("Line coverage: %.2f%% (%d/%d lines), required: %.2f%%\n", $actual, $covered, $statements, $required);

if ($covered * 100 < $required * $statements)
{
	fwrite(STDERR, "Line coverage is below the required minimum.\n");
	exit(1);
}

exit(0);
