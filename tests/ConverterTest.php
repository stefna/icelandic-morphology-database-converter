<?php declare(strict_types=1);

namespace Tests\Stefna\DIMConverter;

use PHPUnit\Framework\TestCase;
use Stefna\DIMConverter\Converter;
use Stefna\DIMConverter\Options;
use Symfony\Component\Console\Output\NullOutput;

final class ConverterTest extends TestCase
{
	private $outputFile;

	public function testConvert(): void
	{
		$converter = $this->createConverter([]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(512, $lines);
	}

	public function testConvertFilterDomain(): void
	{
		$converter = $this->createConverter([
			Options::FILTER_DOMAIN => 'alm',
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(289, $lines);
	}

	public function testConvertFilterDomainBasic(): void
	{
		$converter = $this->createConverter([
			Options::FILTER_DOMAIN_BASIC => true,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(407, $lines);
	}

	public function testConvertFilterMaxCorrectnessWord(): void
	{
		$converter = $this->createConverter([
			Options::FILTER_MAX_CORRECTNESS_WORD => 1,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(462, $lines);
	}

	public function testConvertFilterMaxCorrectnessInflection(): void
	{
		$converter = $this->createConverter([
			Options::FILTER_MAX_CORRECTNESS_INFLECTIONAL => 1,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(511, $lines);
	}

	public function testConvertFilterGenre(): void
	{
		$converter = $this->createConverter([
			Options::FILTER_GENRE_WORD => 'OFOR',
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(11, $lines);
	}

	public function testConvertFilterGenreBasic(): void
	{
		$converter = $this->createConverter([
			Options::FILTER_GENRE_WORD_BASIC => true,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(489, $lines);
	}

	public function testConvertFilterVisibility(): void
	{
		$converter = $this->createConverter([
			Options::FILTER_VISIBILITY => 'K',
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(51, $lines);
	}

	public function testConvertFilterGenreInflectional(): void
	{
		$converter = $this->createConverter([
			Options::FILTER_GENRE_INFLECTIONAL => 'URE',
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(1, $lines);
	}

	public function testConvertFilterGenreInflectionalBasic(): void
	{
		$converter = $this->createConverter([
			Options::FILTER_GENRE_INFLECTIONAL_BASIC => true,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(511, $lines);
	}

	public function testConvertFilterValue(): void
	{
		$converter = $this->createConverter([
			Options::FILTER_VALUE_INFLECTIONAL => 'HLID',
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(1, $lines);
	}

	public function testConvertAddAlternative(): void
	{
		$converter = $this->createConverter([
			Options::ADD_ALTERNATIVE_ENTRIES => true,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(513, $lines);
	}

	public function testConvertNoDuplicates(): void
	{
		$converter = $this->createConverter([]);

		$converter->convert(__DIR__ . '/fixtures/kristin-duplicates.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(1, $lines);
	}

	public function testConvertMergeDuplicates(): void
	{
		$converter = $this->createConverter([
			Options::MERGE => true,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(505, $lines);
	}

	public function testConvertOutputFormatElastic(): void
	{
		$converter = $this->createConverter([
			Options::OUTPUT_FORMAT_ELASTIC => true,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-duplicates.csv', $this->outputFile);

		$line = trim(file($this->outputFile)[0]);
		$this->assertSame('aa-fundurinn => aa-fundur', $line);
	}

	public function testConvertOutputFormatSolr(): void
	{
		$converter = $this->createConverter([
			Options::OUTPUT_FORMAT_SOLR => true,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-duplicates.csv', $this->outputFile);

		$line = trim(file($this->outputFile)[0]);
		$this->assertSame("aa-fundurinn\taa-fundur", $line);
	}

	public function testConvertOutputFormatHunspell(): void
	{
		$converter = $this->createConverter([
			Options::OUTPUT_FORMAT_HUNSPELL => true,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-duplicates.csv', $this->outputFile);

		$dicFile = $this->outputFile . '.dic';
		$affFile = $this->outputFile . '.aff';
		$dicLines = array_map('trim', file($dicFile));
		$affLines = array_map('trim', file($affFile));
		$this->assertContains('SET UTF-8', $affLines);
		$this->assertSame("1", $dicLines[0]);
		$this->assertSame("aa-fundur/1", $dicLines[1]);
		$this->assertContains("SFX 1 0 inn .", $affLines);
	}

	public function testConvertCaseSensitive(): void
	{
		$converter = $this->createConverter([
			Options::CASE_SENSITIVE => true,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-duplicates.csv', $this->outputFile);

		$line = trim(file($this->outputFile)[0]);
		$this->assertSame("AA-fundurinn\tAA-fundur", $line);
	}

	public function testConvertSpecialMerge(): void
	{
		$converter = $this->createConverter([]);

		$converter->convert(__DIR__ . '/fixtures/kristin-þingvellir.csv', $this->outputFile);

		$lines = array_map('trim', array_filter(file($this->outputFile)));
		$entries = [];
		foreach ($lines as $line) {
			$list = explode("\t", $line);
			$entries[$list[0]][] = $list[1];
		}
		$this->assertCount(14, $entries);

		$this->assertSame(['þingvellir', 'þingvöllur'], $entries['þingvalla']);
	}

	protected function setUp(): void
	{
		parent::setUp();

		$this->outputFile = tempnam(sys_get_temp_dir(), 'dim-test-');
	}

	protected function tearDown(): void
	{
		parent::tearDown();
		if (is_file($this->outputFile)) {
			unlink($this->outputFile);
		}
	}

	private function createConverter(array $options): Converter
	{
		return Converter::createFromOptionsArray(new NullOutput(), $options);
	}
}
