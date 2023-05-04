<?php declare(strict_types=1);

namespace Stefna\DIMConverter\OutputWriter;

use Psr\Log\LoggerInterface;
use Stefna\DIMConverter\Config\Config;

final class OutputWriterFactory
{
	public const FORMAT_ELASTIC = 'elastic';
	public const FORMAT_HUNSPELL = 'hunspell';
	public const FORMAT_SOLR = 'solr';
	private LoggerInterface $logger;

	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function createFromConfig(Config $config): OutputWriterInterface
	{
		if ($config->getOutputFormat() === self::FORMAT_ELASTIC) {
			return new OutputWriterElastic();
		}
		if ($config->getOutputFormat() === self::FORMAT_HUNSPELL) {
			return new OutputWriterHunspell($this->logger, $config->getHunspellComboThreshold());
		}
		return new OutputWriterSolr();
	}
}
