<?php

namespace App;

use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Storage;


class SSMLTransformer
{
    public $html = '';

    public function __construct($html)
    {
        $this->html = $html;
    }

    public function removeTag($tag)
    {
        $crawler = new Crawler($this->html, url('/'));
        $crawler->filter($tag)->each(function (Crawler $crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });

        $this->html = $crawler->html();

        return $this;
    }

    /**
     * Save a file to disk
     *
     * @param $html
     * @param $filename
     */
    public function save($html, $filename)
    {
        Storage::disk('public_uploads')->put($filename, $html);
    }
}
