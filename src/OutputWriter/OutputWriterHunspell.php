<?php declare(strict_types=1);

namespace Stefna\DIMConverter\OutputWriter;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Stefna\DIMConverter\Entity\DataEntry;
use Stefna\DIMConverter\Hunspell\HunspellDbFactory;

final class OutputWriterHunspell implements OutputWriterInterface
{
	private LoggerInterface $logger;

	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function write(string $filename, DataEntry ...$dataEntries): int
	{
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if ($ext) {
			throw new InvalidArgumentException('Hunspell output does not allow extension in output filename');
		}

		$hunspellDb = (new HunspellDbFactory($this->logger))->createFromDataEntries(...$dataEntries);

		$filenameDic = $filename . '.dic';
		$filenameAff = $filename . '.aff';

		$fDic = fopen($filenameDic, 'wb');
		fwrite($fDic, $hunspellDb->getTotal() . "\n");
		foreach ($hunspellDb->getDictLines() as $dictEntry) {
			fwrite($fDic, $dictEntry->toString() . "\n");
		}
		fclose($fDic);

		$fAff = fopen($filenameAff, 'wb');
		foreach ($hunspellDb->getAffHeaders() as $affLine) {
			fwrite($fAff, $affLine . "\n");
		}
		foreach ($hunspellDb->getSfxLines() as $affLine) {
			fwrite($fAff, $affLine . "\n");
		}
		fclose($fAff);

		return $hunspellDb->getTotal();
	}
}
