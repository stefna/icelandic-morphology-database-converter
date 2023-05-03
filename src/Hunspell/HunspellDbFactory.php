<?php declare(strict_types=1);

namespace Stefna\DIMConverter\Hunspell;

use Psr\Log\LoggerInterface;
use Stefna\DIMConverter\Entity\DataEntry;

final class HunspellDbFactory
{
	private LoggerInterface $logger;

	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function createFromDataEntries(DataEntry ...$dataEntries): HunspellDb
	{
		/** @var HunspellStem[] $stemList */
		$stemList = [];
		/** @var array<string, Sfx> $sfxList */
		$sfxList = [];
		/** @var array<int, string> $sfxNumToKey */
		$sfxNumToKey = [];

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
				$stemList[] = $entry;
				continue;
			}
			$words = array_filter($words, static fn($w) => strpos($w, '/') === false);
			foreach ($words as $wordWord) {
				$sfx = $this->createSfx($entry, [$wordWord]);
				$sfxKey = $sfx->getKey();

				$foundSfx = $sfxList[$sfxKey] ?? null;
				if (!$foundSfx) {
					$sfxList[$sfxKey] = $sfx;
					$sfx->setNum($sfxNum);
					$foundSfx = $sfx;
					$sfxNumToKey[$sfxNum] = $sfxKey;
					$sfxNum++;
				}
				$entry->addSfxNum($foundSfx->getNum());
				$foundSfx->incDictEntries();
			}
			$stemList[] = $entry;
		}

		$this->mergeAndOptimize($stemList, $sfxList, $sfxNumToKey, $sfxNum);

		return new HunspellDb($stemList, $sfxList);
	}

	/**
	 * @param HunspellStem[] $stemList
	 * @param array<string, Sfx> $sfxList
	 * @param array<int, string> $sfxNumToKey
	 */
	private function mergeAndOptimize(array $stemList, array &$sfxList, array $sfxNumToKey, int &$sfxNum): void
	{
		/** @var array<array-key, list<HunspellStem>> $combos */
		$combos = [];
		foreach ($stemList as $dicEntry) {
			$sfxNums = $dicEntry->getSfxNums();
			if (count($sfxNums) < 2) {
				continue;
			}
			$sfxComboKey = implode(',', $sfxNums);
			$combos[$sfxComboKey][] = $dicEntry;
		}

		$comboEntriesNumberThreshold = 300;
		foreach ($combos as $comboKey => $comboDicEntries) {
			$countComboDicEntries = count($comboDicEntries);
			if ($countComboDicEntries < $comboEntriesNumberThreshold) {
				continue;
			}

			$sfxNumsFromCombo = explode(',', (string)$comboKey);
			$newSfxDicEntries = [];
			foreach ($sfxNumsFromCombo as $sfxNumForCombo) {
				$sfxNumForCombo = (int)$sfxNumForCombo;
				$sfxKey = $sfxNumToKey[$sfxNumForCombo] ?? null;
				if (!$sfxKey) {
					$this->logger->warning('Missing sfx-key for combo', [
						'num' => $sfxNumForCombo,
					]);
					continue;
				}
				$tmpSfx = $sfxList[$sfxKey] ?? null;
				if (!$tmpSfx) {
					$this->logger->warning('Missing sfx entry for combo', [
						'num' => $sfxNumForCombo,
						'key' => $sfxKey,
					]);
					continue;
				}
				array_push($newSfxDicEntries, ...$tmpSfx->getEntries());
				$tmpSfx->decDictEntries($countComboDicEntries);
			}
			if (!$newSfxDicEntries) {
				$this->logger->warning('No new sfx dict entries found', [
					'combo_key' => $comboKey,
				]);
				continue;
			}
			$newSfx = new Sfx(...$newSfxDicEntries);
			$newSfx->setNum($sfxNum++);
			$newSfx->incDictEntries($countComboDicEntries);
			$newSfxKey = $newSfx->getKey();
			if (isset($sfxList[$newSfxKey])) {
				$this->logger->warning('Duplicate new sfx key in combo', [
					'combo_key' => $comboKey,
					'sfx_key' => $newSfxKey,
				]);
			}
			$sfxList[$newSfxKey] = $newSfx;
			foreach ($comboDicEntries as $dicEntry) {
				$dicEntry->resetSfxNum();
				$dicEntry->addSfxNum($newSfx->getNum());
			}
		}

		$toRemove = [];
		foreach ($sfxList as $sfxKey => $sfx) {
			if ($sfx->getNumDictEntries() <= 0) {
				if ($sfx->getNumDictEntries() < 0) {
					$this->logger->warning('Found a sfx with num-dict-entries < 0', [
						'sfx_key' => $sfx->getKey(),
						'num_dict_entries' => $sfx->getNumDictEntries(),
					]);
				}
				$toRemove[] = $sfxKey;
			}
		}
		foreach ($toRemove as $sfxKey) {
			unset($sfxList[$sfxKey]);
		}
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
