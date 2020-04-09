<?php

namespace App;

use Illuminate\Support\Facades\Storage;

class SSMLTransformer
{
    public $content = '';

    public function __construct($html)
    {
        $this->content = $html;
    }

    public function appendTo($tag, $where)
    {
        $html = '<ssml>'.$this->content.'</ssml>';

        $html = \FluentDOM($html)
            ->find('//' . $where)
            ->append($tag);

        $this->content = preg_replace('/^.+\n/', '', (string)$html->formatOutput());
        $this->content = preg_replace('/\<\/?ssml\>\\n/', '', (string)$this->content);

        $this->content = html_entity_decode($this->content);

        return $this;

    }

    public function appendAttr($tag, $attr)
    {
        $firstKey = array_key_first($attr);
        $html = '<ssml>'.$this->content.'</ssml>';

        $html = \FluentDOM($html);

        $html->find('//' . $tag)
            ->attr[$firstKey] = $attr[$firstKey];

        $this->content = preg_replace('/^.+\n/', '', (string)$html->formatOutput());
        $this->content = preg_replace('/\<\/?ssml\>\\n/', '', (string)$this->content);

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
        $html = '<ssml>'.$this->content.'</ssml>';

        $html = \FluentDOM($html)
            ->find('//' . $tag)
            ->remove();

        $this->content = preg_replace('/^.+\n/', '', (string)$html);
        $this->content = preg_replace('/<ssml>/', '', (string)$this->content);
        $this->content = preg_replace('/\<\/ssml\>\\n/', '', (string)$this->content);

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
