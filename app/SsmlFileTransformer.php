<?php

namespace App;

use Illuminate\Support\Facades\Storage;

/**
 * Class SsmlFileTransformer
 * @package App
 */
class SsmlFileTransformer
{
    /**
     * @var string
     */
    public $content = '';
    /**
     * @var \FluentDOM
     */
    public $dom;

    public function __construct($html)
    {
        $this->content = $html;

        $this->dom = \FluentDOM($this->content, 'text/html');
    }

    /**
     * Add SSML Break Tags
     *
     * @param $tag
     * @return $this
     */
    public function addBreakTags($tag)
    {
        $this->content = preg_replace("/(<\/$tag>)/", '$1<break></break>', (string)$this->content);

        return $this;
    }

    /**
     * Add Time Attribute to SSML Break Tags
     *
     * @param $value
     * @return $this
     */
    public function addBreakTagTimeAttr($value)
    {
        $this->content = preg_replace('/<break><\/break>/', '<break time="' . $value . '"></break>', (string)$this->content);

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
        $html = $this->dom
            ->find('//' . $tag)
            ->remove();

        $this->sanitizeSsmlContent($html);

        return $this;
    }

    /**
     * Replace strong
     *
     * @return $this
     */
    public function replaceStrong()
    {
        $this->content = preg_replace('/(<)(\/)?strong(>)/', '', (string)$this->content);

        return $this;
    }

    /**
     * Replace emphasis
     *
     * @return $this
     */
    public function replaceEmphasis()
    {
        $this->content = preg_replace('/(<)(\/)?em(>)/', '', (string)$this->content);

        return $this;
    }

    /**
     * Replace lists
     *
     * @return $this
     */
    public function replaceLists()
    {
        $this->content = preg_replace('/(<)li(>)/', '$1p$2', (string)$this->content);
        $this->content = preg_replace('/(<\/)li(>)/', '$1p$2' . '<break time="800ms"></break>', (string)$this->content);
        $this->content = preg_replace('/(<)(\/)?ul(>)/', '', (string)$this->content);

        return $this;
    }


    /**
     * Replace headers
     *
     * @return $this
     */
    public function replaceHeaders()
    {
        $this->content = preg_replace('/<h[\d]{1}>/', '<p>', (string)$this->content);
        $this->content = preg_replace('/<\/h[\d]{1}>/', '</p><break time="1200ms"></break>', (string)$this->content);

        return $this;
    }

    /**
     * Replace headers
     *
     * @return $this
     */
    public function replaceGlossary()
    {
        $this->content = preg_replace('/<\/?dl>/', '', (string)$this->content);

        $this->content = preg_replace('/(<)dt(>)/', '$1p$2', (string)$this->content);
        $this->content = preg_replace('/(<\/)dt(>)/', '$1p$2' . '<break time="800ms"></break>', (string)$this->content);

        $this->content = preg_replace('/(<)dd(>)/', '$1p$2', (string)$this->content);
        $this->content = preg_replace('/(<)dd style="margin-bottom: 10px;"(>)/', '$1p$2', (string)$this->content);
        $this->content = preg_replace('/(<\/)dd(>)/', '$1p$2' . '<break time="800ms"></break>', (string)$this->content);

        return $this;
    }

    /**
     * Replace apostrophes
     *
     * @return $this
     */
    public function replaceApostrophes()
    {
        $this->content = preg_replace('/’/', '\'', (string)$this->content);

        return $this;
    }

    /**
     * Replace dashes
     *
     * @return $this
     */
    public function replaceDashes()
    {
        $this->content = preg_replace('/-/', '<break time="100ms"></break>', (string)$this->content);
        $this->content = preg_replace('/—/', '<break time="100ms"></break>', (string)$this->content);

        return $this;
    }

    /**
     * Replace fractions
     *
     * @return $this
     */
    public function replaceFractions()
    {
        $this->content = preg_replace('/(\d+)(½)/', '<say-as interpret-as="fraction">$1+1/2</say-as>', (string)$this->content);
        $this->content = preg_replace('/(\d+)&half;/', '<say-as interpret-as="fraction">$1+1/2</say-as>', (string)$this->content);
        $this->content = preg_replace('/(\d+)&amp;half;/', '<say-as interpret-as="fraction">$1+1/2</say-as>', (string)$this->content);

        return $this;
    }

    /**
     * Replace quotes
     *
     * @return $this
     */
    public function replaceColons()
    {
        $this->content = preg_replace('/:/', '<break time="100ms"></break>', (string)$this->content);

        return $this;
    }

    /**
     * Replace quotes
     *
     * @return $this
     */
    public function replaceQuotes()
    {
        $this->content = preg_replace('/“/', '', (string)$this->content);
        $this->content = preg_replace('/”/', '', (string)$this->content);

        return $this;
    }

    /**
     * Create SSML File
     *
     * @param $filename
     * @return $this
     */
    public function create($filename)
    {
        $this->replaceTags();

        $this->replaceCharacters();

        $this->save($filename);

        return $this;
    }

    /**
     * Wrap all content with a certain content
     *
     * @param $tag
     * @return $this
     */
    public function wrapAll($tag)
    {
        $this->content = "<$tag>" . $this->content . "</$tag>";

        return $this;
    }

    /**
     * Save a file to disk
     *
     * @param $filename
     */
    public function save($filename)
    {
        Storage::disk('public_uploads')->put($filename, $this->content);
    }

    /**
     * Sanitize SSML Content
     *
     * @param \FluentDOM\Query $html
     */
    protected function sanitizeSsmlContent(\FluentDOM\Query $html): void
    {
        $this->content = preg_replace('/^.+\r/', '', (string)$html);
        $this->content = preg_replace('/^.+\n/', '', (string)$html);
        $this->content = preg_replace('/<html>/', '', $this->content);
        $this->content = preg_replace('/<body>/', '', (string)$this->content);
        $this->content = preg_replace('/\<\/html\>/', '', (string)$this->content);
        $this->content = preg_replace('/\<\/body\>/', '', (string)$this->content);
        $this->content = preg_replace('/\\n/', '', (string)$this->content);
    }

    /**
     * Replace Characters
     */
    protected function replaceCharacters(): void
    {
        $this
            ->replaceHeaders()
            ->replaceEmphasis()
            ->replaceStrong()
            ->replaceLists()
            ->replaceGlossary()
            ->replaceApostrophes()
            ->replaceQuotes()
            ->replaceColons()
            ->replaceDashes()
            ->replaceFractions();
    }

    /**
     * Replace/Transform tags
     */
    protected function replaceTags(): void
    {
        $this
            ->removeTag('br')
            ->removeTag('table')
            ->removeTag('img')
            ->removeTag('figure')
            ->addBreakTags('p')
            ->addBreakTagTimeAttr('800ms')
            ->wrapAll('speak');
    }
}
