<?php declare(strict_types=1);

namespace Stefna\DIMConverter\Config;

use Stefna\DIMConverter\Entity\LineFactory;
use Stefna\DIMConverter\Options;

final class ConfigFactory
{
	public function createFromArray(array $options): Config
	{
		$ret = Config::create();

		if (isset($options[Options::FILTER_DOMAIN])) {
			$ret = $ret->withFilterDomain(array_map('strtolower', explode(',', $options[Options::FILTER_DOMAIN])) ?: null);
		}
		elseif ($options[Options::FILTER_DOMAIN_BASIC] ?? false) {
			$ret = $ret->withFilterDomain(Options::BASIC_DOMAIN_LIST);
		}

		if (isset($options[Options::FILTER_MAX_CORRECTNESS_WORD])) {
			$ret = $ret->withFilterMaxCorrectnessWord((int)$options[Options::FILTER_MAX_CORRECTNESS_WORD]);
		}

		if (isset($options[Options::FILTER_GENRE_WORD])) {
			$ret = $ret->withFilterGenreWord(array_map('strtolower', explode(',', $options[Options::FILTER_GENRE_WORD])) ?: null);
		}
		elseif ($options[Options::FILTER_GENRE_WORD_BASIC] ?? false) {
			$ret = $ret->withFilterGenreWord(Options::BASIC_GENRE_LIST);
		}

		if (isset($options[Options::FILTER_VISIBILITY])) {
			$ret = $ret->withFilterVisibility(strtoupper($options[Options::FILTER_VISIBILITY])[0]);
		}

		if (isset($options[Options::FILTER_MAX_CORRECTNESS_INFLECTIONAL])) {
			$ret = $ret->withFilterMaxCorrectnessInflectional((int)$options[Options::FILTER_MAX_CORRECTNESS_INFLECTIONAL]);
		}

		if (isset($options[Options::FILTER_GENRE_INFLECTIONAL])) {
			$ret = $ret->withFilterGenreInflectional(array_map('strtolower', explode(',', $options[Options::FILTER_GENRE_INFLECTIONAL])) ?: null);
		}
		elseif ($options[Options::FILTER_GENRE_INFLECTIONAL_BASIC] ?? false) {
			$ret = $ret->withFilterGenreInflectional(Options::BASIC_GENRE_LIST);
		}

		if (isset($options[Options::FILTER_VALUE_INFLECTIONAL])) {
			$ret = $ret->withFilterValueInflectional(array_map('strtolower', explode(',', $options[Options::FILTER_VALUE_INFLECTIONAL])) ?: null);
		}
		elseif ($options[Options::FILTER_VALUE_INFLECTIONAL_BASIC] ?? false) {
			$ret = $ret->withFilterValueInflectional(Options::BASIC_VALUE_LIST);
		}

		$ret = $ret->withAddAlternativeEntries((bool)($options[Options::ADD_ALTERNATIVE_ENTRIES] ?? false));

		$ret = $ret->withMerge((bool)($options[Options::MERGE] ?? false));

		$ret = $ret->withCaseSensitive((bool)($options[Options::CASE_SENSITIVE] ?? false));

		$inputFormat = $options[Options::INPUT_FORMAT] ?? null;
		if ($inputFormat === 'S' || $inputFormat === 's') {
			$ret = $ret->withInputFormat(LineFactory::FORMAT_SIGRUN);
		}

		return $ret;
	}
}
