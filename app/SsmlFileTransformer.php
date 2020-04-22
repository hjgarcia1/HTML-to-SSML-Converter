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

    public function appendTo($tag, $where)
    {
        $html = $this->dom
            ->find('//' . $where)
            ->after($tag);

        $this->sanitizeSsmlContent($html);

        return $this;

    }

    public function appendAttr($tag, $attr)
    {
        $firstKey = array_key_first($attr);

        $html = $this->dom;

        $html->find('//' . $tag)
            ->attr[$firstKey] = $attr[$firstKey];

        $this->sanitizeSsmlContent($html);

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
        $this->content = preg_replace('/-/', '<break time="250ms"></break>', (string)$this->content);
        $this->content = preg_replace('/—/', '<break time="250ms"></break>', (string)$this->content);

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
        $this
            ->removeTag('br')
            ->removeTag('table')
            ->removeTag('img')
            ->removeTag('figure')
            ->appendTo('<break />', 'p')
            ->appendAttr('break', ['time' => '800ms'])
            ->wrapAll('speak');

        $this
            ->replaceHeaders()
            ->replaceEmphasis()
            ->replaceStrong()
            ->replaceLists()
            ->replaceGlossary()
            ->replaceApostrophes()
            ->replaceQuotes()
            ->replaceDashes();

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
        $this->content  = "<$tag>" . $this->content . "</$tag>";

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
}
