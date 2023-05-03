<?php declare(strict_types=1);

namespace Stefna\DIMConverter\Hunspell;

final class HunspellStem
{
	private string $stem;
	/** @var list<int> */
	private array $sfxNum = [];

	public function __construct(string $stem)
	{
		$this->stem = $stem;
	}

	public function getStem(): string
	{
		return $this->stem;
	}

	public function addSfxNum(int $num): void
	{
		$this->sfxNum[] = $num;
	}

	public function toString(): string
	{
		if (!$this->sfxNum) {
			return $this->stem;
		}
		$tmp = $this->sfxNum;
		sort($tmp, SORT_NUMERIC);
		return implode('/', [
			$this->stem,
			implode(',', $tmp),
		]);
	}
}
