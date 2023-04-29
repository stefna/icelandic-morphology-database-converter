<?php declare(strict_types=1);

namespace Stefna\DIMConverter\OutputWriter;

use Stefna\DIMConverter\Config\Config;

final class OutputWriterFactory
{
	public const FORMAT_ELASTIC = 'elastic';
	public const FORMAT_HUNSPELL = 'solr';
	public const FORMAT_SOLR = 'solr';

	public function __construct()
	{
	}

	public function createFromConfig(Config $config): OutputWriterInterface
	{
		if ($config->getOutputFormat() === self::FORMAT_ELASTIC) {
			return new OutputWriterElastic();
		}
		if ($config->getOutputFormat() === self::FORMAT_HUNSPELL) {
			return new OutputWriterHunspell();
		}
		return new OutputWriterSolr();
	}
}
