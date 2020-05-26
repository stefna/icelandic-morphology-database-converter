<?php declare(strict_types=1);

namespace Stefna\DIMConverter\OutputWriter;

use Stefna\DIMConverter\Entity\DataEntry;

interface OutputWriterInterface
{
	public function write(string $filename, DataEntry ...$dataEntries): int;
}
