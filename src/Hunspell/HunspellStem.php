<?php declare(strict_types=1);

namespace Stefna\DIMConverter\Hunspell;

final class HunspellStem
{
	private string $stem;
	private int $sfxNum;

	public function __construct(string $stem)
	{
		$this->stem = $stem;
	}

	public function getStem(): string
	{
		return $this->stem;
	}

	public function setSfxNum(int $num): void
	{
		$this->sfxNum = $num;
	}

	public function toString(): string
	{
		if (!isset($this->sfxNum)) {
			return $this->stem;
		}
		return implode('/', [
			$this->stem,
			$this->sfxNum,
		]);
	}
}
