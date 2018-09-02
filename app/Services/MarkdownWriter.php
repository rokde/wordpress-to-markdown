<?php

namespace App\Services;

use League\HTMLToMarkdown\HtmlConverter;

class MarkdownWriter
{
	private $frontmatter = [];

	private $content = null;
	/**
	 * @var string
	 */
	private $filename;

	static public function create(string $filename, array $post)
	{
		$writer = new static($filename);
		$writer->frontmatter = [
			'title' => array_get($post, 'title'),
			'author' => array_get($post, 'author'),
			'excerpt' => array_get($post, 'excerpt'),
			'categories' => implode(', ', array_get($post, 'categories', [])),
			'tags' => implode(', ', array_get($post, 'tags', [])),
			'permalink' => array_get($post, 'permalink'),
		];

		$converter = new HtmlConverter();
		$writer->content = $converter->convert(array_get($post, 'content'));
		if (empty($writer->frontmatter['excerpt'])) {
			$writer->frontmatter['excerpt'] = str_limit($writer->content);
		}

		return $writer;
	}

	private function __construct(string $filename)
	{
		$this->filename = $filename;
	}

	public function write()
	{
		if (!is_dir(dirname($this->filename))) {
			mkdir(dirname($this->filename), 0777, true);
		}

		$fh = fopen($this->filename, 'wb+');
		$this->writeFrontMatter($fh);
		fwrite($fh, $this->content);
		fclose($fh);
	}

	private function writeFrontMatter($fh)
	{
		$this->writeLine($fh, '---');
		foreach ($this->frontmatter as $key => $value) {
			$this->writeLine($fh, $key . ': ' . $value);
		}
		$this->writeLine($fh, '---');
		$this->writeLine($fh);
	}

	private function writeLine($fh, string $line = '')
	{
		fwrite($fh, $line . PHP_EOL);
	}
}