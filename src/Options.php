<?php declare(strict_types=1);

namespace Stefna\DIMConverter;

final class Options
{
	public const OUTPUT_FILE = 'output-file';
	public const INPUT_FILE = 'input-file';
	public const INPUT_FORMAT = 'input-format';
	public const OUTPUT_FORMAT_ELASTIC = 'output-format-elastic';
	public const OUTPUT_FORMAT_SOLR = 'output-format-solr';
	public const OUTPUT_FORMAT_HUNSPELL = 'output-format-hunspell';
	public const FILTER_GENRE_WORD_BASIC = 'filter-genre-word-basic';
	public const FILTER_DOMAIN_BASIC = 'filter-domain-basic';
	public const FILTER_MAX_CORRECTNESS_INFLECTIONAL = 'filter-max-correctness-inflectional';
	public const FILTER_DOMAIN = 'filter-domain';
	public const FILTER_MAX_CORRECTNESS_WORD = 'filter-max-correctness-word';
	public const FILTER_GENRE_INFLECTIONAL = 'filter-genre-inflectional';
	public const FILTER_VISIBILITY = 'filter-visibility';
	public const FILTER_VALUE_INFLECTIONAL = 'filter-value-inflectional';
	public const FILTER_VALUE_INFLECTIONAL_BASIC = 'filter-value-inflectional-basic';
	public const FILTER_GENRE_INFLECTIONAL_BASIC = 'filter-genre-inflectional-basic';
	public const FILTER_GENRE_WORD = 'filter-genre-word';
	public const BASIC_DOMAIN_LIST = [
		'alm',
		'ffl',
		'gjald',
		'mat',
		'mæl',
		'titl',
		'tími',
		'tung',
		'við',
		'ism',
		'gæl',
		'föð',
		'móð',
		'bær',
		'göt',
		'lönd',
		'þor',
		'örn',
		'fyr',
		'mvirk',
	];
	public const BASIC_GENRE_LIST = ['', 'form', 'gam', 'ofor', 'stad'];
	public const BASIC_VALUE_LIST = ['', 'jafn', 'rik', 'reik', 'hlid', 'merk1', 'merk2', 'osb'];
	public const MERGE = 'merge';
	public const ADD_ALTERNATIVE_ENTRIES = 'add-alternative-entries';
	public const CASE_SENSITIVE = 'case-sensitive';
}
