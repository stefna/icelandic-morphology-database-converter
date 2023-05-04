<?php declare(strict_types=1);

namespace Tests\Stefna\DIMConverter\Hunspell;

use Stefna\DIMConverter\Hunspell\HunspellDb;
use PHPUnit\Framework\TestCase;

class HunspellDbTest extends TestCase
{
	public function provideSfxParts(): array
	{
		return [
			[
				'bróðir', 'bræður', ['óðir', 'æður'],
			],
			[
				'hestur', 'hesturinn', ['0', 'inn'],
			],
			[
				'fundur', 'fund', ['ur', '0'],
			],
			[
				'eins', 'eins', [],
			],
		];
	}

	/**
	 * @dataProvider provideSfxParts
	 */
	public function testFindSfxParts(string $stem, string $word, array $expected): void
	{
		$actual = HunspellDb::findSfxParts($stem, $word);
		$this->assertSame($expected, $actual);
	}
}
