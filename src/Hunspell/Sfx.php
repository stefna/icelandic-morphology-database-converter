<?php declare(strict_types=1);

namespace Stefna\DIMConverter\Hunspell;

final class Sfx
{
	/**
	 * @var SfxEntry[]
	 */
	private array $entries;
	private string $key;
	private int $num;
	private int $numDictEntries = 0;

	public function __construct(SfxEntry ...$entries)
	{
		$this->entries = $entries;
		$this->key = $this->createKey();
	}

	public function getKey(): string
	{
		return $this->key;
	}

	public function getNum(): int
	{
		return $this->num;
	}

	public function setNum(int $param): void
	{
		$this->num = $param;
	}

	/**
	 * @return array<string, SfxEntry>
	 */
	public function getEntries(): array
	{
		return $this->entries;
	}

	public function incDictEntries(int $num = 1): void
	{
		$this->numDictEntries += $num;
	}

	public function decDictEntries(int $num): void
	{
		$this->numDictEntries -= $num;
	}

	public function getNumDictEntries(): int {
		return $this->numDictEntries;
	}

	private function createKey(): string
	{
		$items = [];
		foreach ($this->entries as $entry) {
			$item = implode('=', [
				$entry->getStrip(),
				$entry->getReplace(),
			]);
			$items[] = $item;
		}
		return implode('|', $items);
	}
}
