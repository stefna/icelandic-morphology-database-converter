<?php declare(strict_types=1);

namespace Stefna\DIMConverter;

use DateTimeImmutable;
use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LocalConsoleLogger extends AbstractLogger
{
	public const NOITCE = 'comment';
	public const INFO = 'info';
	public const ERROR = 'error';
	public const DEBUG = 'question';
	private OutputInterface $output;
	private $verbosityLevelMap = [
		LogLevel::EMERGENCY => OutputInterface::VERBOSITY_NORMAL,
		LogLevel::ALERT => OutputInterface::VERBOSITY_NORMAL,
		LogLevel::CRITICAL => OutputInterface::VERBOSITY_NORMAL,
		LogLevel::ERROR => OutputInterface::VERBOSITY_NORMAL,
		LogLevel::WARNING => OutputInterface::VERBOSITY_NORMAL,
		LogLevel::NOTICE => OutputInterface::VERBOSITY_VERBOSE,
		LogLevel::INFO => OutputInterface::VERBOSITY_VERY_VERBOSE,
		LogLevel::DEBUG => OutputInterface::VERBOSITY_DEBUG,
	];
	private $formatLevelMap = [
		LogLevel::EMERGENCY => self::ERROR,
		LogLevel::ALERT => self::ERROR,
		LogLevel::CRITICAL => self::ERROR,
		LogLevel::ERROR => self::ERROR,
		LogLevel::WARNING => self::NOITCE,
		LogLevel::NOTICE => self::NOITCE,
		LogLevel::INFO => self::INFO,
		LogLevel::DEBUG => self::DEBUG,
	];

	public function __construct(OutputInterface $output, array $verbosityLevelMap = [], array $formatLevelMap = [])
	{
		$this->output = $output;
		$this->verbosityLevelMap = $verbosityLevelMap + $this->verbosityLevelMap;
		$this->formatLevelMap = $formatLevelMap + $this->formatLevelMap;
	}

	public function log($level, $message, array $context = []): void
	{
		if (!isset($this->verbosityLevelMap[$level])) {
			throw new InvalidArgumentException(sprintf('The log level "%s" does not exist.', $level));
		}

		$output = $this->output;

		// Write to the error output if necessary and available
		if ((self::ERROR === $this->formatLevelMap[$level]) && $this->output instanceof ConsoleOutputInterface) {
			/** @noinspection PhpPossiblePolymorphicInvocationInspection */
			$output = $output->getErrorOutput();
		}

		// the if condition check isn't necessary -- it's the same one that $output will do internally anyway.
		// We only do it for efficiency here as the message formatting is relatively expensive.
		if ($output->getVerbosity() >= $this->verbosityLevelMap[$level]) {
			$output->writeln(
				sprintf(
					'<%1$s>%4$s [%2$s] %3$s</%1$s>',
					$this->formatLevelMap[$level],
					$level,
					$message . $this->formatContext($context),
					(new DateTimeImmutable())->format('Y-m-d\TH:i:s.v'),
				),
				$this->verbosityLevelMap[$level],
			);
		}
	}

	private function formatContext(array $context): string
	{
		if (!$context) {
			return '';
		}
		return ': ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
	}
}
