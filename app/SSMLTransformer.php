<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use function foo\func;

class SSMLTransformer
{
    public $content = '';

    public function __construct($html)
    {
        $this->content = $html;
    }

    public function appendTo($tag, $where)
    {
        $html = \FluentDOM($this->content, 'text/html')
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

        $html = \FluentDOM($this->content, 'text/html');

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
        $html = \FluentDOM($this->content, 'text/html')
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
     * Save a file to disk
     *
     * @param $filename
     */
    public function save($filename)
    {
        Storage::disk('public_uploads')->put($filename, $this->content);
    }
}
