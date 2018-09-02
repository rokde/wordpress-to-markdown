<?php

namespace App\Services;

class FilenameResolver
{
	/**
	 * @var string
	 */
	private $dateFormat;
	/**
	 * @var string
	 */
	private $extension;

	public function __construct(string $dateFormat, string $extension)
	{
		$this->dateFormat = $dateFormat;
		$this->extension = ltrim($extension, '.');
	}

	public function resolve(array $post): string
	{
		return (array_get($post, 'type') === 'post'
				? $this->getDatePrefix($post) . '.'
				: '')
			. $this->getFilename($post);
	}

	private function getDatePrefix(array $post): string
	{
		return array_has($post, 'pubDate')
			? array_get($post, 'pubDate')->format('Y-m-d')
			: '';
	}

	/**
	 * uses slug or title to return a filename with extension
	 *
	 * @param array $post
	 * @return string
	 */
	private function getFilename(array $post): string
	{
		return array_get($post, 'slug', str_slug(array_get($post, 'title')))
			. '.' . $this->extension;
	}
}