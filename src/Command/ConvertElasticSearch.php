<?php declare(strict_types=1);

namespace Stefna\DIMConverter\Command;

use Stefna\DIMConverter\Converter;
use Stefna\DIMConverter\Options;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ConvertElasticSearch extends Command
{
	protected function configure(): void
	{
		parent::configure();

		$this->setName('elastic');
		$this->setAliases(['es']);
		$this->setDescription('Information about terminology can be found here https://bin.arnastofnun.is/DMII/LTdata/k-format/');

		$this->addArgument(Options::INPUT_FILE, InputArgument::REQUIRED, 'The input CSV file');
		$this->addArgument(Options::OUTPUT_FILE, InputArgument::REQUIRED, 'The output file');

		$this->addOption(Options::INPUT_FORMAT, 'I', InputOption::VALUE_REQUIRED, 'Which format is the input. K or S');
		$this->addOption(Options::FILTER_DOMAIN, null, InputOption::VALUE_REQUIRED, 'Comma separated list of domains');
		$this->addOption(Options::FILTER_DOMAIN_BASIC, 'DB', InputOption::VALUE_NONE, 'Preset for basic domain');
		$this->addOption(Options::FILTER_MAX_CORRECTNESS_WORD, null, InputOption::VALUE_REQUIRED, '1-5 - 5 means 0');
		$this->addOption(Options::FILTER_GENRE_WORD, null, InputOption::VALUE_REQUIRED, 'CSV of genres (FORM,FORN,...)');
		$this->addOption(Options::FILTER_GENRE_WORD_BASIC, 'GB', InputOption::VALUE_NONE, 'Basic preset for genre');

		$this->addOption(Options::FILTER_VISIBILITY, null, InputOption::VALUE_REQUIRED, 'CSV of visibilities (K=core, V=rest)');

		$this->addOption(Options::FILTER_MAX_CORRECTNESS_INFLECTIONAL, null, InputOption::VALUE_REQUIRED, '1-5 of the inflectional word - 5 means 0');
		$this->addOption(Options::FILTER_GENRE_INFLECTIONAL, null, InputOption::VALUE_REQUIRED, 'CSV of genres (FORM,FORN,...)');
		$this->addOption(Options::FILTER_GENRE_INFLECTIONAL_BASIC, 'IB', InputOption::VALUE_NONE, 'Basic preset for genre inflactional');

		$this->addOption(Options::FILTER_VALUE_INFLECTIONAL, null, InputOption::VALUE_REQUIRED, 'CSV of values (JAFN,RIK,...)');
		$this->addOption(Options::FILTER_VALUE_INFLECTIONAL_BASIC, 'VB', InputOption::VALUE_NONE, 'Basic preset for value of inflactional form');

		$this->addOption(Options::ADD_ALTERNATIVE_ENTRIES, null, InputOption::VALUE_NONE, 'Should the alternative entry be added if available');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$inputFilename = $input->getArgument(Options::INPUT_FILE);
		if (!is_file($inputFilename)) {
			throw new InvalidArgumentException('Must provide an existing input file');
		}
		$outputFilename = $input->getArgument(Options::OUTPUT_FILE);
		if (is_file($outputFilename)) {
			throw new \InvalidArgumentException('The output file must not exist before run');
		}

		$converter = Converter::createFromInput($input);

		$converter->setOutput($output);
		return $converter->convert($inputFilename, $outputFilename);
	}
}