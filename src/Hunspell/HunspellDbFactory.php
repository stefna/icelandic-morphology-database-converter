<?php declare(strict_types=1);

namespace Stefna\DIMConverter\Hunspell;

use Stefna\DIMConverter\Entity\DataEntry;

final class HunspellDbFactory
{
	public function createFromDataEntries(DataEntry ...$dataEntries): HunspellDb
	{
		$dic = $sfx = [];
		foreach ($dataEntries as $dataEntry) {
			$word = $dataEntry->getWord();
			$words = $dataEntry->getWords();
			$words = array_unique($words);
			$tmp = array_search($word, $words, true);
			if ($tmp !== false) {
				unset($words[$tmp]);
			}
			if (count($words) < 1) {
				$dic[] = $word;
				continue;
			}
			$sfx[] = [
				'word' => $word,
				'words' => $words,
			];
			$sfxNum = count($sfx);
			$dic[] = "$word/$sfxNum";
		}

		return new HunspellDb($dic, $sfx);
	}
}
