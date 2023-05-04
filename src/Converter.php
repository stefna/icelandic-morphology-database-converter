<?php declare(strict_types=1);

namespace Stefna\DIMConverter;

use Generator;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
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
	private Config $config;
	private OutputWriterInterface $outputWriter;
	private Filter $filter;
	private LineFactory $lineFactory;
	private LoggerInterface $logger;

	public static function create(
		Config $config,
		OutputWriterInterface $outputWriter,
		Filter $filter,
		LoggerInterface $logger
	): self {
		return new self($config, $outputWriter, $filter, $logger);
	}

	public static function createFromOptionsArray(OutputInterface $output, array $options): self
	{
		$configFactory = new ConfigFactory();
		$logger = new LocalConsoleLogger($output);
		$outputWriterFactory = new OutputWriterFactory(
			$logger,
		);
		$filterFactory = new FilterFactory();

		$config = $configFactory->createFromArray($options);
		$outputWriter = $outputWriterFactory->createFromConfig($config);
		$filter = $filterFactory->createFromConfig($config);

		return self::create($config, $outputWriter, $filter, $logger);
	}

	private function __construct(
		Config $config,
		OutputWriterInterface $outputWriter,
		Filter $filter,
		LoggerInterface $logger
	) {
		$this->config = $config;
		$this->outputWriter = $outputWriter;
		$this->filter = $filter;
		$this->logger = $logger;

		$this->lineFactory = new LineFactory($config);
	}

	public function convert(string $inputFilename, string $outputFilename): int
	{
		/** @var DataEntry[] $data */
		$data = [];
		$lineNo = 0;
		$caseSensitive = $this->config->isCaseSensitive();

		/** @var Line $line */
		foreach ($this->readLine($inputFilename) as $line) {
			$lineNo++;
			if (!$this->filter->filter($line)) {
				$this->logger->info('Filter out line while reading', [
					'lineNo' => $lineNo,
				]);
				continue;
			}
			$id = $line->getId();
			$word = $line->getWord();
			if (!$caseSensitive) {
				$word = mb_strtolower($word);
			}
			if (!isset($data[$id])) {
				$data[$id] = DataEntry::create($word);
			}
			$inflectionalForm = $line->getInflectionalForm();
			if (!$caseSensitive) {
				$inflectionalForm = mb_strtolower($inflectionalForm);
			}
			if ($inflectionalForm === $word) {
				continue;
			}
			$data[$id]->add($inflectionalForm);
			if ($this->config->isAddAlternativeEntries() && $alt = $line->getAlternativeEntry()) {
				if (!$caseSensitive) {
					$alt = mb_strtolower($alt);
				}
				$data[$id]->add($alt);
			}
		}
		if (!$data) {
			return 1;
		}

		if ($this->config->isMerge()) {
			$data = $this->merge($data);
		}

		$this->logger->notice('Start writing output', [
			'to' => $outputFilename,
			'count' => count($data),
		]);
		return $this->outputWriter->write($outputFilename, ...array_values($data));
	}

	private function readLine(string $inputFilename): Generator
	{
		$f = fopen($inputFilename, 'rb');
		if (!$f) {
			throw new InvalidArgumentException('Could not open input file');
		}
		$this->logger->notice('Starting to read from file', [
			'file' => $inputFilename,
		]);
		$lineNo = 0;
		try {
			while ($line = fgets($f)) {
				$lineNo++;
				$this->logger->debug('Reading line', [
					'line' => $line,
				]);
				try {
					yield $this->lineFactory->create($line);
				}
				catch (Throwable $e) {
					$this->logger->warning('Error while reading line', [
						'line' => $lineNo,
						'e' => $e->getMessage(),
					]);
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
		$this->logger->notice('Start merging of data entries', [
			'num_entries' => count($data),
		]);
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
}
