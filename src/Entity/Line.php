<?php declare(strict_types=1);

namespace Stefna\DIMConverter\Entity;

use InvalidArgumentException;

final class Line
{
	private string $word;
	private int $id;
	private string $wordClass;
	private string $domain;
	private int $correctnessWord;
	private ?string $genreWord;
	private ?string $grammar;
	private ?int $crossReference;
	private string $visibility;
	private string $inflectionalForm;
	private string $grammaticalTag;
	private int $correctnessInflectional;
	private ?string $genreInflectional;
	private ?string $valueOfInflectionalForm;
	private ?string $alternativeEntry;

	public static function createKristinarForm(string $line): self
	{
		$ret = new self();
		$line = trim($line);
		$parts = explode(';', $line);
		if (count($parts) < 15) {
			throw new InvalidArgumentException('Bad input line');
		}
		$ret->word = array_shift($parts);
		$ret->id = (int)array_shift($parts);
		$ret->wordClass = array_shift($parts);
		$ret->domain = array_shift($parts);
		$ret->correctnessWord = (int)array_shift($parts);
		$ret->genreWord = strtolower(array_shift($parts));
		$ret->grammar = array_shift($parts);
		$ret->crossReference = (int)array_shift($parts);
		$ret->visibility = array_shift($parts);
		$ret->inflectionalForm = array_shift($parts);
		$ret->grammaticalTag = array_shift($parts);
		$ret->correctnessInflectional = (int)array_shift($parts);
		$ret->genreInflectional = strtolower(array_shift($parts));
		$ret->valueOfInflectionalForm = strtolower(array_shift($parts));
		$ret->alternativeEntry = array_shift($parts);
		return $ret;
	}

	public static function createSigrunarForm(string $line): self
	{
		$ret = new self();
		$line = trim($line);
		$parts = explode(';', $line);
		if (count($parts) < 6) {
			throw new InvalidArgumentException('Bad input line');
		}
		$ret->word = array_shift($parts);
		$ret->id = (int)array_shift($parts);
		$ret->wordClass = array_shift($parts);
		$ret->domain = array_shift($parts);
		$ret->inflectionalForm = array_shift($parts);
		$ret->grammar = array_shift($parts);
		return $ret;
	}

	private function __construct()
	{
	}

	public function getWord(): string
	{
		return $this->word;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getWordClass(): string
	{
		return $this->wordClass;
	}

	public function getDomain(): string
	{
		return $this->domain;
	}

	public function getCorrectnessWord(): int
	{
		return $this->correctnessWord;
	}

	public function getGenreWord(): ?string
	{
		return $this->genreWord;
	}

	public function getGrammar(): ?string
	{
		return $this->grammar;
	}

	public function getCrossReference(): ?int
	{
		return $this->crossReference;
	}

	public function getVisibility(): string
	{
		return $this->visibility;
	}

	public function getInflectionalForm(): string
	{
		return $this->inflectionalForm;
	}

	public function getGrammaticalTag(): string
	{
		return $this->grammaticalTag;
	}

	public function getCorrectnessInflectional(): int
	{
		return $this->correctnessInflectional;
	}

	public function getGenreInflectional(): ?string
	{
		return $this->genreInflectional;
	}

	public function getValueOfInflectionalForm(): ?string
	{
		return $this->valueOfInflectionalForm;
	}

	public function getAlternativeEntry(): ?string
	{
		return $this->alternativeEntry;
	}
}
