<?php

namespace App\Commands;

use App\Services\FilenameResolver;
use App\Services\MarkdownWriter;
use App\Services\Wordpress;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Laravie\Parser\Xml\Document;
use Laravie\Parser\Xml\Reader;

class ConvertWordpressToMarkdownCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'convert 
                            {xml : Wordpress XML File}
                            {output : Write the markdown files in this directory}
                            {--force : Overwrite already found files}
                            {--format=Y-m-d : Date format for blog posts}
                            {--extension=md : Extension for markdown files}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Converts a wordpress xml file to markdown files';

	/**
	 * @var Wordpress
	 */
    private $wp;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
    	$xml = $this->argument('xml');
		$this->task('Loading ' . $xml, function () use ($xml) {
			return $this->loadXml($xml);
		});

	    $directory = $output = $this->argument('output');
		if (starts_with($output, '.')) {
			$directory = realpath(__DIR__. '/../../') . ltrim($output, '.');
		}

		$this->task('Writing ' . $this->wp->all()->count() . ' posts to '. $directory, function () use ($directory) {
			return $this->writePosts($directory);
		});
    }

    private function loadXml(string $xmlFile)
    {
	    $this->wp = Wordpress::fromFile($xmlFile);

    	return true;
    }

    private function writePosts(string $directory)
    {
    	$this->output->newLine();
    	$filenameResolver = new FilenameResolver(
    		$directory,
		    $this->option('format', 'Y-m-d'),
		    $this->option('extension', 'md')
	    );

    	$this->wp->all()->each(function (array $post) use ($filenameResolver) {
    		$filename = $filenameResolver->resolve($post);

    		$this->info('  ' . $filename);

    		MarkdownWriter::create($filename, $post)
		        ->write($this->option('force'));
	    });
    	return true;
    }
}
