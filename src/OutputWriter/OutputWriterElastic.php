<?php declare(strict_types=1);

namespace Stefna\DIMConverter\OutputWriter;

use Stefna\DIMConverter\Entity\DataEntry;

final class OutputWriterElastic implements OutputWriterInterface
{
	public function write(string $filename, DataEntry ...$dataEntries): int
	{
		$f = fopen($filename, 'wb');
		$total = 0;
		foreach ($dataEntries as $dataEntry) {
			$word = $dataEntry->getWord();
			foreach ($dataEntry->getWords() as $other) {
				fwrite($f, "$other => $word\n");
				$total++;
			}
		}
		fclose($f);
		return $total;
	}
}
