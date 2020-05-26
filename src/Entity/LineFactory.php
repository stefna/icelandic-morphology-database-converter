<?php declare(strict_types=1);

namespace Stefna\DIMConverter\Entity;

use Stefna\DIMConverter\Config\Config;

final class LineFactory
{
	const FORMAT_KRISTIN = 'kristin';
	const FORMAT_SIGRUN = 'sigrun';
	private bool $useSigrunarForm;

	public function __construct(Config $config)
	{
		$this->useSigrunarForm = $config->getInputFormat() === self::FORMAT_SIGRUN;
	}

	public function create(string $line): Line
	{
		if ($this->useSigrunarForm) {
			return Line::createSigrunarForm($line);
		}
		return Line::createKristinarForm($line);
	}
}
