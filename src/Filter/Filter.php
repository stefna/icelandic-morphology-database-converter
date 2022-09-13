<?php declare(strict_types=1);

namespace Stefna\DIMConverter\Filter;

use Stefna\DIMConverter\Config\Config;
use Stefna\DIMConverter\Entity\Line;

final class Filter
{
	private Config $config;

	public static function create(Config $config): self
	{
		return new self($config);
	}

	private function __construct(Config $config)
	{
		$this->config = $config;
	}

	public function filter(Line $line): bool
	{
		return $this->filterDomain($line)
			&& $this->filterMaxCorrectnessWord($line)
			&& $this->filterGenreWord($line)
			&& $this->filterVisibility($line)
			&& $this->filterCorrectnessInflectional($line)
			&& $this->filterGenreInflectional($line)
			&& $this->filterValueInflectional($line);
	}

	private function filterDomain(Line $line): bool
	{
		$domain = $this->config->getFilterDomain();
		return $domain === null || in_array($line->getDomain(), $domain, true);
	}

	private function filterMaxCorrectnessWord(Line $line): bool
	{
		$correctnessWord = $line->getCorrectnessWord();
		if ($correctnessWord === 0) {
			$correctnessWord = 5;
		}
		return $correctnessWord <= $this->config->getFilterMaxCorrectnessWord();
	}

	private function filterGenreWord(Line $line): bool
	{
		$word = $this->config->getFilterGenreWord();
		return $word == null || in_array($line->getGenreWord(), $word, true);
	}

	private function filterVisibility(Line $line): bool
	{
		$visibility = $this->config->getFilterVisibility();
		return $visibility === null || $line->getVisibility() === $visibility;
	}

	private function filterCorrectnessInflectional(Line $line): bool
	{
		$correctnessInflectional = $line->getCorrectnessInflectional();
		if ($correctnessInflectional === 0) {
			$correctnessInflectional = 5;
		}
		return $correctnessInflectional <= $this->config->getFilterMaxCorrectnessInflectional();
	}

	private function filterGenreInflectional(Line $line): bool
	{
		$genreInflectional = $this->config->getFilterGenreInflectional();
		return $genreInflectional === null || in_array($line->getGenreInflectional(), $genreInflectional, true);
	}

	private function filterValueInflectional(Line $line): bool
	{
		$valueInflectional = $this->config->getFilterValueInflectional();
		return $valueInflectional === null || in_array($line->getValueOfInflectionalForm(), $valueInflectional, true);
	}
}
