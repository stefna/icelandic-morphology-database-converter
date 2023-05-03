<?php declare(strict_types=1);

namespace Stefna\DIMConverter\Hunspell;

final class SfxEntry
{
	private string $strip;
	private string $replace;

	public function __construct(string $strip, string $replace)
	{
		$this->strip = $strip;
		$this->replace = $replace;
	}

	public function getStrip(): string
	{
		return $this->strip;
	}

	public function getReplace(): string
	{
		return $this->replace;
	}
}
