<?php declare(strict_types=1);

namespace Stefna\DIMConverter;

use Generator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Converter
{
	private ?OutputInterface $output = null;
	private ?array $filterDomain = null;
	private int $filterMaxCorrectnessWord = 5;
	private ?array $filterGenreWord = null;
	private ?string $filterVisibility = null;
	private int $filterMaxCorrectnessInflectional = 5;
	private ?array $filterGenreInflectional = null;
	private ?array $filterValueInflectional = null;
	private bool $addAlternativeEntries = false;

	public static function createFromInput(InputInterface $input): self
	{
		return self::createFromArray($input->getOptions());
	}

	public static function createFromArray(array $options): self
	{
		$ret = new self();
		if (isset($options[Options::FILTER_DOMAIN])) {
			$ret->filterDomain = array_map('strtolower', explode(',', $options[Options::FILTER_DOMAIN])) ?: null;
		}
		elseif ($options[Options::FILTER_DOMAIN_BASIC] ?? false) {
			$ret->filterDomain = Options::BASIC_DOMAIN_LIST;
		}

		if (isset($options[Options::FILTER_MAX_CORRECTNESS_WORD])) {
			$ret->filterMaxCorrectnessWord = (int)$options[Options::FILTER_MAX_CORRECTNESS_WORD];
		}

		if (isset($options[Options::FILTER_GENRE_WORD])) {
			$ret->filterGenreWord = array_map('strtolower', explode(',', $options[Options::FILTER_GENRE_WORD])) ?: null;
		}
		elseif ($options[Options::FILTER_GENRE_WORD_BASIC] ?? false) {
			$ret->filterGenreWord = Options::BASIC_GENRE_LIST;
		}

		if (isset($options[Options::FILTER_VISIBILITY])) {
			$ret->filterVisibility = strtoupper($options[Options::FILTER_VISIBILITY])[0];
		}

		if (isset($options[Options::FILTER_MAX_CORRECTNESS_INFLECTIONAL])) {
			$ret->filterMaxCorrectnessInflectional = (int)$options[Options::FILTER_MAX_CORRECTNESS_INFLECTIONAL];
		}

		if (isset($options[Options::FILTER_GENRE_INFLECTIONAL])) {
			$ret->filterGenreInflectional = array_map('strtolower', explode(',', $options[Options::FILTER_GENRE_INFLECTIONAL])) ?: null;
		}
		elseif ($options[Options::FILTER_GENRE_INFLECTIONAL_BASIC] ?? false) {
			$ret->filterGenreInflectional = Options::BASIC_GENRE_LIST;
		}

		if (isset($options[Options::FILTER_VALUE_INFLECTIONAL])) {
			$ret->filterValueInflectional = array_map('strtolower', explode(',', $options[Options::FILTER_VALUE_INFLECTIONAL])) ?: null;
		}
		elseif ($options[Options::FILTER_VALUE_INFLECTIONAL_BASIC] ?? false) {
			$ret->filterValueInflectional = Options::BASIC_VALUE_LIST;
		}

		$ret->addAlternativeEntries = (bool)($options[Options::ADD_ALTERNATIVE_ENTRIES] ?? false);
		return $ret;
	}

	private function __construct()
	{
	}

	public function setOutput(?OutputInterface $output): void
	{
		$this->output = $output;
	}

	public function convert(string $inputFilename, string $outputFilename): int
	{
		/** @var DataEntry[] $data */
		$data = [];
		$lineNo = 0;
		/** @var Line $line */
		foreach ($this->readLine($inputFilename) as $line) {
			$lineNo++;
			if (!$this->filter($line)) {
				$this->output && $this->output->writeln(sprintf('Filter out line %d', $lineNo), OutputInterface::VERBOSITY_DEBUG);
				continue;
			}
			$word = $line->getWord();
			if (!isset($data[$word])) {
				$data[$word] = DataEntry::create($word);
			}
			$inflectionalForm = $line->getInflectionalForm();
			if ($inflectionalForm === $word) {
				continue;
			}
			$data[$word]->add($inflectionalForm);
			if ($this->addAlternativeEntries && $alt = $line->getAlternativeEntry()) {
				$data[$word]->add($alt);
			}
		}
		if (!$data) {
			return 1;
		}

		return $this->outputToFile($outputFilename, ...array_values($data));
	}

	private function readLine(string $inputFilename): Generator
	{
		$f = fopen($inputFilename, 'rb');
		if (!$f) {
			throw new \InvalidArgumentException('Could not open input file');
		}
		$this->output && $this->output->writeln(sprintf('Starting to read from %s', $inputFilename), OutputInterface::VERBOSITY_DEBUG);
		$lineNo = 0;
		try {
			while ($line = fgets($f)) {
				$lineNo++;
				$this->output && $this->output->writeln(sprintf('Reading line %d', $lineNo), OutputInterface::VERBOSITY_DEBUG);
				try {
					yield Line::create($line);
				}
				catch (\Throwable $e) {
					// just ignore, or perhaps log?
					$this->output && $this->output->writeln(sprintf('<warn>Problem in line %d</warn>', $lineNo));
				}
			}
		}
		finally {
			fclose($f);
		}
	}

	private function outputToFile(string $outputFilename, DataEntry ...$data): int
	{
		$f = fopen($outputFilename, 'wb');
		$total = 0;
		foreach ($data as $dataEntry) {
			$word = $dataEntry->getWord();
			foreach ($dataEntry->getWords() as $other) {
				fwrite($f, "$word => $other\n");
				$total++;
			}
		}
		fclose($f);
		$this->output && $this->output->writeln(sprintf('Added %d lines to %s', $total, $outputFilename), OutputInterface::VERBOSITY_DEBUG);
		return 0;
	}

	private function filter(Line $line): bool
	{
		if (($this->filterDomain !== null) && !in_array($line->getDomain(), $this->filterDomain, true)) {
			return false;
		}
		$correctnessWord = $line->getCorrectnessWord();
		if ($correctnessWord === 0) {
			$correctnessWord = 5;
		}
		if ($correctnessWord > $this->filterMaxCorrectnessWord) {
			return false;
		}

		if ($this->filterGenreWord !== null && !in_array($line->getGenreWord(), $this->filterGenreWord, true)) {
			return false;
		}

		if ($this->filterVisibility && $line->getVisibility() !== $this->filterVisibility) {
			return false;
		}

		$correctnessInflectional = $line->getCorrectnessInflectional();
		if ($correctnessInflectional === 0) {
			$correctnessInflectional = 5;
		}
		if ($correctnessInflectional > $this->filterMaxCorrectnessInflectional) {
			return false;
		}

		if ($this->filterGenreInflectional !== null && !in_array($line->getGenreInflectional(), $this->filterGenreInflectional, true)) {
			return false;
		}

		if ($this->filterValueInflectional !== null && !in_array($line->getValueOfInflectionalForm(), $this->filterValueInflectional, true)) {
			return false;
		}
		return true;
	}
}
