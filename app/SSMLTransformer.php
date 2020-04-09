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

    public function appendTag($tag)
    {
        $crawler = new Crawler($this->html, url('/'));



        $crawler->filterXPath('//div')->each(function (Crawler $divs) use ($tag, $crawler) {
            foreach ($divs as $div) {
                $dom = $crawler->getNode(0)->parentNode;
                //creating div
                $div = $dom->createElement($tag);

                $element = $dom->parentNode->createElement($tag);
                $div->appendChild($element);
            }
        });

        $this->html = $crawler->html();

        dd($this->html);

        return $this;
    }

    /**
     * Remove a tag
     *
     * @param $tag
     * @return $this
     */
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
