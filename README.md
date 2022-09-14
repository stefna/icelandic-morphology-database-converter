# Icelandic Morphology Database Converter

This tool can be used to create a stemmer dictionary file from a file in [Kristinar form](https://bin.arnastofnun.is/DMII/LTdata/k-format) coming from [Árnastofnun](https://www.arnastofnun.is).


The output file should be used with the `StemmerOverrideFilterFactory` of Solr or ElasticSearch
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

## Contribute

We are always happy to receive bug/security reports and bug/security fixes
