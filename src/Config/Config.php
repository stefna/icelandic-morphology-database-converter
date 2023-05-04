<?php declare(strict_types=1);

namespace Stefna\DIMConverter\Config;

use Stefna\DIMConverter\Entity\LineFactory;
use Stefna\DIMConverter\OutputWriter\OutputWriterFactory;

final class Config
{
	private ?array $filterDomain = null;
	private int $filterMaxCorrectnessWord = 5;
	private ?array $filterGenreWord = null;
	private ?string $filterVisibility = null;
	private int $filterMaxCorrectnessInflectional = 5;
	private ?array $filterGenreInflectional = null;
	private ?array $filterValueInflectional = null;
	private bool $addAlternativeEntries = false;
	private bool $merge = false;
	private bool $caseSensitive = false;
	private string $outputFormat = OutputWriterFactory::FORMAT_SOLR;
	private string $inputFormat = LineFactory::FORMAT_KRISTIN;
	private int $hunspellComboThreshold = 300;

	public static function create(): self
	{
		return new self();
	}

	private function __construct()
	{
	}

	public function withFilterDomain(?array $param): self
	{
		$clone = clone $this;
		$clone->filterDomain = $param;
		return $clone;
	}

	public function withFilterMaxCorrectnessWord(int $param): self
	{
		$clone = clone $this;
		$clone->filterMaxCorrectnessWord = $param;
		return $clone;
	}

	public function withAddAlternativeEntries(bool $param): self
	{
		$clone = clone $this;
		$clone->addAlternativeEntries = $param;
		return $clone;
	}

	public function withFilterGenreInflectional(?array $param): self
	{
		$clone = clone $this;
		$clone->filterGenreInflectional = $param;
		return $clone;
	}

	public function withFilterGenreWord(?array $param): self
	{
		$clone = clone $this;
		$clone->filterGenreWord = $param;
		return $clone;
	}

	public function withFilterMaxCorrectnessInflectional(int $param): self
	{
		$clone = clone $this;
		$clone->filterMaxCorrectnessInflectional = $param;
		return $clone;
	}

	public function withFilterValueInflectional(?array $param): self
	{
		$clone = clone $this;
		$clone->filterValueInflectional = $param;
		return $clone;
	}

	public function withFilterVisibility(?string $param): self
	{
		$clone = clone $this;
		$clone->filterVisibility = $param;
		return $clone;
	}

	public function withMerge(bool $param): self
	{
		$clone = clone $this;
		$clone->merge = $param;
		return $clone;
	}

	public function withCaseSensitive(bool $param): self
	{
		$clone = clone $this;
		$clone->caseSensitive = $param;
		return $clone;
	}

	public function withHunspellComboThreshold(int $param): self
	{
		$clone = clone $this;
		$clone->hunspellComboThreshold = $param;
		return $clone;
	}

	public function withOutputFormat(string $param): self
	{
		$allowed = [
			OutputWriterFactory::FORMAT_ELASTIC,
			OutputWriterFactory::FORMAT_SOLR,
			OutputWriterFactory::FORMAT_HUNSPELL,
		];
		if (!in_array($param, $allowed, true)) {
			return $this;
		}
		$clone = clone $this;
		$clone->outputFormat = $param;
		return $clone;
	}

	public function withInputFormat(string $param): self
	{
		if ($param !== LineFactory::FORMAT_KRISTIN || $param !== LineFactory::FORMAT_SIGRUN) {
			return $this;
		}
		$clone = clone $this;
		$clone->outputFormat = $param;
		return $clone;
	}

	public function getFilterDomain(): ?array
	{
		return $this->filterDomain;
	}

	public function getFilterMaxCorrectnessWord(): int
	{
		return $this->filterMaxCorrectnessWord;
	}

	public function getFilterGenreWord(): ?array
	{
		return $this->filterGenreWord;
	}

	public function getFilterVisibility(): ?string
	{
		return $this->filterVisibility;
	}

	public function getFilterMaxCorrectnessInflectional(): int
	{
		return $this->filterMaxCorrectnessInflectional;
	}

	public function getFilterGenreInflectional(): ?array
	{
		return $this->filterGenreInflectional;
	}

	public function getFilterValueInflectional(): ?array
	{
		return $this->filterValueInflectional;
	}

	public function isAddAlternativeEntries(): bool
	{
		return $this->addAlternativeEntries;
	}

	public function isMerge(): bool
	{
		return $this->merge;
	}

	public function getOutputFormat(): string
	{
		return $this->outputFormat;
	}

	public function getInputFormat(): string
	{
		return $this->inputFormat;
	}

	public function isCaseSensitive(): bool
	{
		return $this->caseSensitive;
	}

	public function getHunspellComboThreshold(): int
	{
		return $this->hunspellComboThreshold;
	}
}
