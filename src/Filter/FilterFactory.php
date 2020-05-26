<?php declare(strict_types=1);

namespace Stefna\DIMConverter\Filter;

use Stefna\DIMConverter\Config\Config;

final class FilterFactory
{
	public function createFromConfig(Config $config): Filter
	{
		return Filter::create($config);
	}
}
