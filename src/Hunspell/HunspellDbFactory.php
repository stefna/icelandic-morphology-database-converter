<?php declare(strict_types=1);

namespace Stefna\DIMConverter\Hunspell;

use Stefna\DIMConverter\Entity\DataEntry;

final class HunspellDbFactory
{
	public function createFromDataEntries(DataEntry ...$dataEntries): HunspellDb
	{
		/** @var HunspellStem[] $dic */
		$dic = [];
		/** @var array<string, Sfx> $sfxList */
		$sfxList = [];

		$sfxNum = 1;
		foreach ($dataEntries as $dataEntry) {
			$word = $dataEntry->getWord();
			if (strpos($word, '/') !== false) {
				## Must not have words with slashes. We could escape, but perhaps later
				continue;
			}
			$entry = new HunspellStem($word);
			$words = $dataEntry->getWords();
			$words = array_unique($words);
			$tmp = array_search($word, $words, true);
			if ($tmp !== false) {
				unset($words[$tmp]);
			}
			if (count($words) < 1) {
				$dic[] = $entry;
				continue;
			}
			$words = array_filter($words, static fn($w) => strpos($w, '/') === false);
			$sfx = $this->createSfx($entry, $words);
			$sfxKey = $sfx->getKey();

			$foundSfx = $sfxList[$sfxKey] ?? null;
			if (!$foundSfx) {
				$sfxList[$sfxKey] = $sfx;
				$sfx->setNum($sfxNum++);
				$foundSfx = $sfx;
			}
			$entry->setSfxNum($foundSfx->getNum());
			$dic[] = $entry;
		}

		return new HunspellDb($dic, $sfxList);
	}

	private function createSfx(HunspellStem $entry, array $words): Sfx
	{
		$stem = $entry->getStem();

		$sfxEntries = [];
		foreach ($words as $word) {
			[$strip, $replace] = HunspellDb::findSfxParts($stem, $word);
			$sfxEntries[] = new SfxEntry($strip, $replace);
		}
		return new Sfx(...$sfxEntries);
	}
}
