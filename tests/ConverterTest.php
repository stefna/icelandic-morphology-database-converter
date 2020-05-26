<?php declare(strict_types=1);

namespace Tests\Stefna\DIMConverter;

use PHPUnit\Framework\TestCase;
use Stefna\DIMConverter\Converter;
use Stefna\DIMConverter\Options;

final class ConverterTest extends TestCase
{
	private $outputFile;

	public function testConvert(): void
	{
		$converter = Converter::createFromArray([]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(512, $lines);
	}

	public function testConvertFilterDomain(): void
	{
		$converter = Converter::createFromArray([
			Options::FILTER_DOMAIN => 'alm',
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(289, $lines);
	}

	public function testConvertFilterDomainBasic(): void
	{
		$converter = Converter::createFromArray([
			Options::FILTER_DOMAIN_BASIC => true,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(407, $lines);
	}

	public function testConvertFilterMaxCorrectnessWord(): void
	{
		$converter = Converter::createFromArray([
			Options::FILTER_MAX_CORRECTNESS_WORD => 1,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(462, $lines);
	}

	public function testConvertFilterMaxCorrectnessInflection(): void
	{
		$converter = Converter::createFromArray([
			Options::FILTER_MAX_CORRECTNESS_INFLECTIONAL => 1,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(511, $lines);
	}

	public function testConvertFilterGenre(): void
	{
		$converter = Converter::createFromArray([
			Options::FILTER_GENRE_WORD => 'OFOR',
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(11, $lines);
	}

	public function testConvertFilterGenreBasic(): void
	{
		$converter = Converter::createFromArray([
			Options::FILTER_GENRE_WORD_BASIC => true,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(489, $lines);
	}

	public function testConvertFilterVisibility(): void
	{
		$converter = Converter::createFromArray([
			Options::FILTER_VISIBILITY => 'K',
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(51, $lines);
	}

	public function testConvertFilterGenreInflectional(): void
	{
		$converter = Converter::createFromArray([
			Options::FILTER_GENRE_INFLECTIONAL => 'URE',
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(1, $lines);
	}

	public function testConvertFilterGenreInflectionalBasic(): void
	{
		$converter = Converter::createFromArray([
			Options::FILTER_GENRE_INFLECTIONAL_BASIC => true,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(511, $lines);
	}

	public function testConvertFilterValue(): void
	{
		$converter = Converter::createFromArray([
			Options::FILTER_VALUE_INFLECTIONAL => 'HLID',
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(1, $lines);
	}

	public function testConvertAddAlternative(): void
	{
		$converter = Converter::createFromArray([
			Options::ADD_ALTERNATIVE_ENTRIES => true,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(513, $lines);
	}

	public function testConvertNoDuplicates(): void
	{
		$converter = Converter::createFromArray([]);

		$converter->convert(__DIR__ . '/fixtures/kristin-duplicates.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(1, $lines);
	}

	public function testConvertMergeDuplicates(): void
	{
		$converter = Converter::createFromArray([
			Options::MERGE => true,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-1000.csv', $this->outputFile);

		$lines = file($this->outputFile);
		$this->assertCount(505, $lines);
	}

	public function testConvertCorrectFormat(): void
	{
		$converter = Converter::createFromArray([
			Options::MERGE => true,
		]);

		$converter->convert(__DIR__ . '/fixtures/kristin-duplicates.csv', $this->outputFile);

		$line = trim(file($this->outputFile)[0]);
		$this->assertSame('AA-fundurinn => AA-fundur', $line);
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
}
