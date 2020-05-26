<?php declare(strict_types=1);

namespace Stefna\DIMConverter;

use Stefna\DIMConverter\Command\ConvertElasticSearch;

final class Application extends \Symfony\Component\Console\Application
{
	public function __construct(string $name = 'UNKNOWN', string $version = 'UNKNOWN')
	{
		parent::__construct($name, $version);


		$this->addCommands([
			new ConvertElasticSearch(),
		]);
	}
}
