<?php

namespace App\Services;

use Illuminate\Support\Collection;

class Wordpress
{
	private $posts;

	public function __construct()
	{
		$this->posts = collect();
	}

	static public function fromFile(string $file): self
	{
		return (new static())->load(file_get_contents($file));
	}

	public function load(string $wordpressXml): self
	{
		$xml = simplexml_load_string($wordpressXml);

		foreach ($xml->channel->item as $item) {
			$categories = [];
			$tags = [];
			foreach ($item->category as $category) {
				$nicename = (string)$category['nicename'];
				$categoryLabel = (string)$category;

				if ($nicename != 'uncategorized' && $category['domain'] == 'category') {
					$categories[$nicename] = $categoryLabel;
				} elseif ($category['domain'] == 'post_tag') {
					$tags[$nicename] = $categoryLabel;
				}
			}
			$content = $item->children('http://purl.org/rss/1.0/modules/content/');

			$excerpt = $item->children('http://wordpress.org/export/1.2/excerpt/');

			$wp = $item->children('http://wordpress.org/export/1.2/');

			$dc = $item->children('http://purl.org/dc/elements/1.1/');

			$this->posts->push([
				'type' => (string)$wp->post_type,
				'author' => (string)$dc->creator,
				'title' => (string)$item->title,
				'content' => (string)$content->encoded,
				'excerpt' => (string)$excerpt->encoded,
				'pubDate' => new \DateTime((string)$item->pubDate),
				'categories' => $categories,
				'tags' => $tags,
				'slug' => (string)$wp->post_name,
				'permalink' => (string)$item->guid,
			]);
		}

		return $this;
	}

	public function all(): Collection
	{
		return $this->posts;
	}

	public function posts(): Collection
	{
		return $this->all()->filter(function (array $post) {
			return array_get($post, 'type') === 'post';
		});
	}

	public function pages(): Collection
	{
		return $this->all()->filter(function (array $post) {
			return array_get($post, 'type') === 'page';
		});
	}
}