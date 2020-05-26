<?php declare(strict_types=1);

namespace Stefna\DIMConverter\Entity;

final class DataEntry
{
	private string $word;
	private array $words = [];

	public static function create(string $word): self
	{
		return new self($word);
	}

	private function __construct(string $word)
	{
		$this->word = $word;
	}

	public function add(string $word): void
	{
		$this->words[$word] = $word;
	}

	public function getWord(): string
	{
		return $this->word;
	}

	public function getWords(): array
	{
		return $this->words;
	}

	public function merge(DataEntry $dataEntry): void
	{
		foreach ($dataEntry->getWords() as $word) {
			$this->add($word);
		}
	}
}
