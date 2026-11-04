<?php

declare(strict_types=1);

/**
 * Renders every HTML example to PDF and fails on any PHP error or on a deprecation raised by lib/.
 *
 * Deprecations raised inside vendor/ are reported as warnings only: dependencies are resolved
 * for the minimum PHP version and may emit deprecations on newer PHP without breaking anything.
 * Generated PDFs go to the system temp directory; committed example PDFs are never overwritten.
 *
 * Usage: php .github/scripts/render-examples.php
 * @author Radoslaw Skrzypczak <r.skrzypczak@yetiforce.com>
 */
$root = dirname(__DIR__, 2);
$isCi = false !== getenv('GITHUB_ACTIONS');

error_reporting(E_ALL);
ini_set('display_errors', 'stderr');
ini_set('memory_limit', '1G');

/** @var array<string, array{level: string, count: int}> $reported */
$reported = [];
$libDeprecations = 0;

set_error_handler(static function (int $errno, string $message, string $file, int $line) use ($root, &$reported, &$libDeprecations): bool {
	if (!(error_reporting() & $errno)) {
		return false; // Respect the @ operator.
	}
	if (E_DEPRECATED !== $errno && E_USER_DEPRECATED !== $errno) {
		throw new ErrorException($message, 0, $errno, $file, $line);
	}
	$relative = str_starts_with($file, $root . '/') ? substr($file, strlen($root) + 1) : $file;
	$inVendor = str_starts_with($relative, 'vendor/');
	$key = "{$relative}:{$line}: {$message}";
	if (!isset($reported[$key])) {
		$reported[$key] = ['level' => $inVendor ? 'warning' : 'error', 'count' => 0, 'file' => $relative, 'line' => $line, 'message' => $message];
	}
	++$reported[$key]['count'];
	if (!$inVendor) {
		++$libDeprecations;
	}

	return true;
});

require $root . '/vendor/autoload.php';

$examplesDir = $root . '/examples';
$outputDir = sys_get_temp_dir() . '/yetiforcepdf-examples';
if (!is_dir($outputDir) && !mkdir($outputDir, 0777, true) && !is_dir($outputDir)) {
	fwrite(STDERR, "Cannot create output directory {$outputDir}\n");
	exit(1);
}

$cases = glob($examplesDir . '/*.html') ?: [];
$cases = array_merge($cases, glob($examplesDir . '/*.html.php') ?: []);
sort($cases);

$failures = 0;
$rows = [];
chdir($examplesDir); // Examples reference images and other assets by relative path.
foreach ($cases as $case) {
	$name = preg_replace('/\.html(\.php)?$/', '', basename($case));
	$start = microtime(true);
	try {
		if (str_ends_with($case, '.php')) {
			$html = '';
			require $case; // Defines $html.
		} else {
			$html = file_get_contents($case);
		}
		$document = (new YetiForcePDF\Document())->init();
		$document->loadHtml($html);
		$pdf = $document->render();
		if (!str_starts_with($pdf, '%PDF-')) {
			throw new RuntimeException('Rendered output is not a PDF document.');
		}
		file_put_contents("{$outputDir}/{$name}.pdf", $pdf);
		$pages = preg_match_all('#/Type\s*/Page[^s]#', $pdf);
		$rows[] = sprintf('| %s | ok | %d | %.1f s |', $name, $pages, microtime(true) - $start);
		printf("OK    %-26s pages=%-5d %.1fs\n", $name, $pages, microtime(true) - $start);
	} catch (Throwable $e) {
		++$failures;
		$file = str_starts_with($e->getFile(), $root . '/') ? substr($e->getFile(), strlen($root) + 1) : $e->getFile();
		$rows[] = sprintf('| %s | **FAILED** | | %s |', $name, str_replace('|', '\|', $e->getMessage()));
		printf("FAIL  %-26s %s: %s @ %s:%d\n", $name, get_class($e), $e->getMessage(), $file, $e->getLine());
		if ($isCi) {
			printf("::error file=%s,line=%d,title=%s::%s\n", $file, $e->getLine(), "Example {$name} failed", $e->getMessage());
		}
	}
}

foreach ($reported as $item) {
	printf("%s  [%dx] %s:%d: %s\n", strtoupper($item['level']), $item['count'], $item['file'], $item['line'], $item['message']);
	if ($isCi) {
		printf("::%s file=%s,line=%d,title=Deprecated on PHP %s::%s\n", $item['level'], $item['file'], $item['line'], PHP_VERSION, $item['message']);
	}
}

$summary = sprintf(
	'PHP %s: %d/%d examples rendered, %d deprecation(s) in lib/, %d unique deprecation(s) in vendor/.',
	PHP_VERSION,
	count($cases) - $failures,
	count($cases),
	$libDeprecations,
	count(array_filter($reported, static fn (array $item): bool => 'warning' === $item['level']))
);
echo "\n{$summary}\n";

$stepSummary = getenv('GITHUB_STEP_SUMMARY');
if ($stepSummary) {
	file_put_contents(
		$stepSummary,
		"### Examples on PHP " . PHP_VERSION . "\n\n{$summary}\n\n| Example | Result | Pages | Time / error |\n|---|---|---|---|\n" . implode("\n", $rows) . "\n",
		FILE_APPEND
	);
}

exit(0 === $failures && 0 === $libDeprecations ? 0 : 1);
