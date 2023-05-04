# Icelandic Morphology Database Converter

This tool can be used to create a stemmer dictionary file from a file in [Kristinar form](https://bin.arnastofnun.is/DMII/LTdata/k-format) coming from [Árnastofnun](https://www.arnastofnun.is).


The output file should be used with the `StemmerOverrideFilterFactory` of Solr or ElasticSearch, or with `HunspellStemFilterFactory` and Hunspell dictionary files.

## Install
```shell
# Either in a separate directory, or use --global
composer require stefna/icelandic-morphology-database-converter
```

## Usage
Download the Kristínarsnið file from correct place. **Make sure you read terms of use**. Then run the `./bin/dim-convert` executable. Note that this takes a while. There are more than 6 million lines the source file.

```
Description:
  Convert BIN database to stemmer_override file for Lucene based search engine.
  Information about terminology can be found here https://bin.arnastofnun.is/DMII/LTdata/k-format/

Usage:
  convert [options] [--] <input-file> <output-file>

Arguments:
  input-file                                                                     The input CSV file
  output-file                                                                    The output file

Options:
  -E, --output-format-elastic                                                    Format output for elasticsearch
  -S, --output-format-solr                                                       Format output for solr (default)
  -H, --output-format-hunspell                                                   Format output for hunspell (filename must be the prefix (will generate .dic and .aff files))
  -I, --input-format=INPUT-FORMAT                                                Which format is the input. K or S
      --filter-domain=FILTER-DOMAIN                                              Comma separated list of domains
      --filter-max-correctness-word=FILTER-MAX-CORRECTNESS-WORD                  1-5 - 5 means 0
      --filter-genre-word=FILTER-GENRE-WORD                                      CSV of genres (FORM,FORN,...)
      --filter-visibility=FILTER-VISIBILITY                                      CSV of visibilities (K=core, V=rest)
      --filter-max-correctness-inflectional=FILTER-MAX-CORRECTNESS-INFLECTIONAL  1-5 of the inflectional word - 5 means 0
      --filter-genre-inflectional=FILTER-GENRE-INFLECTIONAL                      CSV of genres (FORM,FORN,...)
      --filter-value-inflectional=FILTER-VALUE-INFLECTIONAL                      CSV of values (JAFN,RIK,...)
      --add-alternative-entries                                                  Should the alternative entry be added if available
      --merge                                                                    Should same inflection forms be merged into the first base form (based on ID)
      --case-sensitive                                                           Should the words keep case (default: no)
      --hunspell-combo-threshold=HUNSPELL-COMBO-THRESHOLD                        Hunspell "combo" optimization threshold [default: 300]
  -h, --help                                                                     Display this help message
  -q, --quiet                                                                    Do not output any message
  -V, --version                                                                  Display this application version
      --ansi                                                                     Force ANSI output
      --no-ansi                                                                  Disable ANSI output
  -n, --no-interaction                                                           Do not ask any interactive question
  -DB, --filter-domain-basic                                                     Preset for basic domain
  -GB, --filter-genre-word-basic                                                 Basic preset for genre
  -IB, --filter-genre-inflectional-basic                                         Basic preset for genre inflactional
  -VB, --filter-value-inflectional-basic                                         Basic preset for value of inflactional form
  -v|vv|vvv, --verbose                                                           Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

```

## Example
```
$ ./bin/dim-convert ~/Downloads/KRISTINsnid.csv my-stemdict.txt
$ wc my-stemdict.txt
  2866501  8599591 85303196  my-stemdict.txt
$ head -n 1 my-stemdict.txt
aa-deildina => aa-deildin
```

```
$ time php8.2 -d memory_limit=1G ./bin/dim-convert -q \
	-H \
	--hunspell-combo-threshold 10 \
	--case-sensitive \
	~/Downloads/KRISTINsnid.csv \
	 output/is_IS
real	1m34,792s
user	1m34,189s
sys		0m0,592s

$ wc -l output/is_IS.*
  25336 is_IS.aff
  331609 is_IS.dic
  356945 total

$ head -n 2 output/is_IS.dic
331608
68-kynslóð/12362

$ tail -n 2 output/is_IS.aff
SFX 12410 a uðust .
SFX 12410 a uðumst .

```

## Hunspell

In order to be able to use the hunspell dictionary files properly, we run an optimization on them. 
There we find some common patterns and join them into separate pattern. 
This is where the `hunspell-combo-threshold` option comes in.

Note that the hunspell files are only usable for stemming, not as actual dictionary files!

Hunspell has a performance issue when using huge aff files. 
Specifically if there are many SFX rules with many entries we see performance degrade exponentially.
Having many SFX rules with a single entry is very fast.
Therefore, we start with creating an SFX rule with only one entry for each possible replacement.
Then we find all combinations that are recurring, and if the number of dict entries is above the configured threshold,
we splice these rules together.
This results in much smaller size of files and without having performance problems.

After some testing it seem that setting the threshold to 100 is the sweetspot.

With the current input file and case-sensitive flag we get <8MB dic file and a 500KB aff file.

If your Solr instance is running in Cloud mode, note that Zookeeper has file size limits that by default are 1MB. But you should be fine with changing that limit to 10MB without any performance issues.

The `HunspellStemFilterFactory` can take in a comma separated list of .dic files, but only one .aff file. So, you can get around the 1MB limit that way instead of increasing the limit in ZK.


## Contribute

We are always happy to receive bug/security reports and bug/security fixes
