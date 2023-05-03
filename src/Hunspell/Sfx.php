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

	private function createKey(): string
	{
		$keys = $values = [];
		foreach ($this->entries as $entry) {
			$keys[] = $entry->getStrip();
			$values[] = $entry->getReplace();
		}
		return json_encode(array_combine($keys, $values), JSON_THROW_ON_ERROR, JSON_UNESCAPED_UNICODE);
	}
}
