<?php declare(strict_types=1);

namespace Stefna\DIMConverter;

use Generator;
use InvalidArgumentException;
use Stefna\DIMConverter\Config\Config;
use Stefna\DIMConverter\Config\ConfigFactory;
use Stefna\DIMConverter\Entity\DataEntry;
use Stefna\DIMConverter\Entity\Line;
use Stefna\DIMConverter\Entity\LineFactory;
use Stefna\DIMConverter\Filter\Filter;
use Stefna\DIMConverter\Filter\FilterFactory;
use Stefna\DIMConverter\OutputWriter\OutputWriterFactory;
use Stefna\DIMConverter\OutputWriter\OutputWriterInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

final class Converter
{
	private ?OutputInterface $output = null;
	/** @var Config */
	private Config $config;
	/** @var OutputWriterInterface */
	private OutputWriterInterface $outputWriter;
	/** @var Filter */
	private Filter $filter;
	/** @var LineFactory */
	private LineFactory $lineFactory;

	public static function create(Config $config, OutputWriterInterface $outputWriter, Filter $filter): self
	{
		return new self($config, $outputWriter, $filter);
	}

	public static function createFromOptionsArray(array $options): self
	{
		$configFactory = new ConfigFactory();
		$outputWriterFactory = new OutputWriterFactory();
		$filterFactory = new FilterFactory();

		$config = $configFactory->createFromArray($options);
		$outputWriter = $outputWriterFactory->createFromConfig($config);
		$filter = $filterFactory->createFromConfig($config);

		return self::create($config, $outputWriter, $filter);
	}

	private function __construct(Config $config, OutputWriterInterface $outputWriter, Filter $filter)
	{
		$this->config = $config;
		$this->outputWriter = $outputWriter;
		$this->filter = $filter;

		$this->lineFactory = new LineFactory($config);
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
			if (!$this->filter->filter($line)) {
				$this->debug(sprintf('Filter out line %d', $lineNo));
				continue;
			}
			$id = $line->getId();
			$word = $line->getWord();
			if (!isset($data[$id])) {
				$data[$id] = DataEntry::create($word);
			}
			$inflectionalForm = $line->getInflectionalForm();
			if ($inflectionalForm === $word) {
				continue;
			}
			$data[$id]->add($inflectionalForm);
			if ($this->config->isAddAlternativeEntries() && $alt = $line->getAlternativeEntry()) {
				$data[$id]->add($alt);
			}
		}
		if (!$data) {
			return 1;
		}

		if ($this->config->isMerge()) {
			$data = $this->merge($data);
		}

		return $this->outputWriter->write($outputFilename, ...array_values($data));
	}

	private function readLine(string $inputFilename): Generator
	{
		$f = fopen($inputFilename, 'rb');
		if (!$f) {
			throw new InvalidArgumentException('Could not open input file');
		}
		$this->debug(sprintf('Starting to read from %s', $inputFilename));
		$lineNo = 0;
		try {
			while ($line = fgets($f)) {
				$lineNo++;
				$this->debug(sprintf('Reading line %d', $lineNo));
				try {
					yield $this->lineFactory->create($line);
				}
				catch (Throwable $e) {
					// just ignore, or perhaps log?
					$this->output && $this->output->writeln(sprintf('<warn>Problem in line %d</warn>', $lineNo));
				}
			}
		}
		finally {
			fclose($f);
		}
	}

	/**
	 * @param DataEntry[] $data
	 * @return DataEntry[]
	 */
	private function merge(array $data): array
	{
		$this->debug(sprintf('Start merging of %d', count($data)));
		ksort($data);

		$seen = [];
		foreach ($data as $id => $dataEntry) {
			foreach ($dataEntry->getWords() as $word) {
				if (isset($seen[$word])) {
					$otherId = $seen[$word];
					if (!isset($data[$otherId])) {
						continue;
					}
					$data[$otherId]->merge($dataEntry);
					unset($data[$id]);
					continue;
				}
				$seen[$word] = $id;
			}
		}
		return $data;
	}

	private function debug(string $msg): void
	{
		$this->output && $this->output->writeln($msg, OutputInterface::VERBOSITY_DEBUG);
	}
}
