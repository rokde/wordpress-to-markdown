# Wordpress to Markdown Converter

This application is based on [Laravel Zero](https://laravel-zero.com/), a
 version of laravel optimized for command line usage.

------

## Documentation

This is a command line tool for converting a wordpress backup xml file into a
 bunch of markdown files for posts and pages (and other types).

## Installation

You have two options. Install it globally into your system and make sure the
 binary `wordpress-to-markdown` is within your `$PATH` variable.
 
The other way is to install it as your project dependency (like phpunit for
 example).

	composer require rokde/wordpress-to-markdown 

Then you can run `vendor/bin/wordpress-to-markdown` within your
 project.

## Usage

Give your wordpress source xml file and the target folder the converter shall
 store the converted files.

	wordpress-to-markdown convert path/to/your/wordpress.xml path/to/store/markdown/

You can force the writing process with the flag `--force`.

Modifying the file extension of your created files is also supported with the option `--extension`. Default is `md`.

Changing the date format or make more folders then th default behaviour does is supported too. Give your date format with the option `--format` and give one of the [possible values](https://secure.php.net/manual/en/function.date.php#refsect1-function.date-parameters). You can also use `/` to make directories. Default is `Y-m-d`.

This setting will only be used for blog posts. Pages and other types do not use any date formatted string in their resulting filename. 

That´s it.

## License

Wordpress to Markdown Converter is an open-source software licensed under the
 [MIT license](https://github.com/rokde/wordpress-to-markdown/blob/master/LICENSE.md).
