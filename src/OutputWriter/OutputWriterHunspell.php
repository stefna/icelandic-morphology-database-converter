<?php declare(strict_types=1);

namespace Stefna\DIMConverter\OutputWriter;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Stefna\DIMConverter\Entity\DataEntry;
use Stefna\DIMConverter\Hunspell\HunspellDbFactory;

final class OutputWriterHunspell implements OutputWriterInterface
{
	private LoggerInterface $logger;
	private int $comboEntriesThreshold;

	public function __construct(LoggerInterface $logger, int $comboEntriesThreshold)
	{
		$this->logger = $logger;
		$this->comboEntriesThreshold = $comboEntriesThreshold;
	}

	public function write(string $filename, DataEntry ...$dataEntries): int
	{
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if ($ext) {
			throw new InvalidArgumentException('Hunspell output does not allow extension in output filename');
		}

		$hunspellDb = (new HunspellDbFactory($this->logger, $this->comboEntriesThreshold))
			->createFromDataEntries(...$dataEntries);

		$filenameDic = $filename . '.dic';
		$filenameAff = $filename . '.aff';

		$numAff = $numDic = 0;

		$fDic = fopen($filenameDic, 'wb');
		fwrite($fDic, $hunspellDb->getTotal() . "\n");
		foreach ($hunspellDb->getDictLines() as $dictEntry) {
			fwrite($fDic, $dictEntry->toString() . "\n");
			$numDic++;
		}
		fclose($fDic);

		$fAff = fopen($filenameAff, 'wb');
		foreach ($hunspellDb->getAffHeaders() as $affLine) {
			fwrite($fAff, $affLine . "\n");
			$numAff++;
		}
		foreach ($hunspellDb->getSfxLines() as $affLine) {
			fwrite($fAff, $affLine . "\n");
			$numAff++;
		}
		fclose($fAff);

		$this->logger->notice('Wrote to hunspell files', [
			'num_dic_lines' => $numDic,
			'num_aff_lines' => $numAff,
		]);
		return $hunspellDb->getTotal();
	}
}
