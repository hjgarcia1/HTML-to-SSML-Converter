<?php

namespace App;

use Illuminate\Support\Facades\Storage;

class SSMLTransformer
{
    public $html = '';

    public function __construct($html)
    {
        $this->html = $html;
    }

    public function appendTo($tag, $where)
    {
        $html = '<ssml>'.$this->html.'</ssml>';

        $html = \FluentDOM($html)
            ->find('//' . $where)
            ->append($tag);

        $this->html = preg_replace('/^.+\n/', '', (string)$html->formatOutput());
        $this->html = preg_replace('/\<\/?ssml\>\\n/', '', (string)$this->html);

        $this->html = html_entity_decode($this->html);

        return $this;

    }

    public function appendAttr($tag, $attr)
    {
        $firstKey = array_key_first($attr);
        $html = '<ssml>'.$this->html.'</ssml>';

        $html = \FluentDOM($html);

        $html->find('//' . $tag)
            ->attr[$firstKey] = $attr[$firstKey];

        $this->html = preg_replace('/^.+\n/', '', (string)$html->formatOutput());
        $this->html = preg_replace('/\<\/?ssml\>\\n/', '', (string)$this->html);

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
        $html = \FluentDOM($this->html)
            ->find('//' . $tag)
            ->remove();

        $this->html = preg_replace('/^.+\n/', '', (string)$html);

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
