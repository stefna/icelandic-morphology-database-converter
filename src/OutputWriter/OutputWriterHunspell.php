<?php declare(strict_types=1);

namespace Stefna\DIMConverter\OutputWriter;

use InvalidArgumentException;
use Stefna\DIMConverter\Entity\DataEntry;
use Stefna\DIMConverter\Hunspell\HunspellDbFactory;

final class OutputWriterHunspell implements OutputWriterInterface
{
	public function write(string $filename, DataEntry ...$dataEntries): int
	{
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if ($ext) {
			throw new InvalidArgumentException('Hunspell output does not allow extension in output filename');
		}

		$hunspellDb = (new HunspellDbFactory())->createFromDataEntries(...$dataEntries);

		$filenameDic = $filename . '.dic';
		$filenameAff = $filename . '.aff';

		$fDic = fopen($filenameDic, 'wb');
		fwrite($fDic, $hunspellDb->getTotal() . "\n");
		foreach ($hunspellDb->getDictLines() as $dictEntry) {
			fwrite($fDic, $dictEntry . "\n");
		}
		fclose($fDic);

		$fAff = fopen($filenameAff, 'wb');
		foreach ($hunspellDb->getAffHeaders() as $affLine) {
			fwrite($fAff, $affLine . "\n");
		}
		foreach ($hunspellDb->getSfx() as $affLine) {
			fwrite($fAff, $affLine . "\n");
		}
		fclose($fAff);

		return $hunspellDb->getTotal();
	}
}
