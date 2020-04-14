<?php

namespace App;

use Illuminate\Support\Facades\Storage;

class SSMLTransformer
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

        $this->content = preg_replace('/^.+\n/', '', (string)$html);
        $this->content = preg_replace('/<html>/', '', $this->content);
        $this->content = preg_replace('/<body>/', '', (string)$this->content);
        $this->content = preg_replace('/\<\/html\>/', '', (string)$this->content);
        $this->content = preg_replace('/\<\/body\>/', '', (string)$this->content);
        $this->content = preg_replace('/\\n/', '', (string)$this->content);

        return $this;

    }

    public function appendAttr($tag, $attr)
    {
        $firstKey = array_key_first($attr);

        $html = $this->dom;

        $html->find('//' . $tag)
            ->attr[$firstKey] = $attr[$firstKey];

        $this->content = preg_replace('/^.+\n/', '', (string)$html);
        $this->content = preg_replace('/<html>/', '', $this->content);
        $this->content = preg_replace('/<body>/', '', (string)$this->content);
        $this->content = preg_replace('/\<\/html\>/', '', (string)$this->content);
        $this->content = preg_replace('/\<\/body\>/', '', (string)$this->content);
        $this->content = preg_replace('/\\n/', '', (string)$this->content);

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

        $this->content = preg_replace('/^.+\n/', '', (string)$html);
        $this->content = preg_replace('/<html>/', '', (string)$this->content);
        $this->content = preg_replace('/<body>/', '', (string)$this->content);
        $this->content = preg_replace('/\<\/html\>/', '', (string)$this->content);
        $this->content = preg_replace('/\<\/body\>/', '', (string)$this->content);
        $this->content = preg_replace('/\\n/', '', (string)$this->content);

        return $this;
    }

    /**
     * Replace lists
     *
     * @param $tag
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
     * @param $tag
     * @return $this
     */
    public function replaceHeaders($tag)
    {
        $this->content = preg_replace('/h[\d]{1}/', $tag, (string)$this->content);
        $this->content = preg_replace('/<\/p><p>/', '</p><break time="1200ms"></break><p>', (string)$this->content);

        return $this;
    }

    /**
     * Replace headers
     *
     * @param $tag
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
     * Remove ap
     */
    public function replaceApostrophes()
    {
        $this->content = preg_replace('/’/', '&apos;', (string)$this->content);

        return $this;
    }

    public function replaceDashes()
    {
        $this->content = preg_replace('/-/', '&ndash;', (string)$this->content);
        $this->content = preg_replace('/—/', '&mdash;', (string)$this->content);

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
}
