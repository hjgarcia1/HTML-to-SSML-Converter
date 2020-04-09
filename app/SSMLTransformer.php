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
        $html = \FluentDOM($this->html)
            ->find('//' . $where)->each(function ($node) use ($tag) {
                $node->append($tag);
            });

        $this->html = preg_replace('/^.+\n/', '', html_entity_decode($html));

        return $this;
    }

    public function appendAttr($tag, $attr)
    {
        $firstKey = array_key_first($attr);

        $html = \FluentDOM($this->html);

        $html->find('//' . $tag)
            ->attr[$firstKey] = $attr[$firstKey];

        $this->html = preg_replace('/^.+\n/', '', (string)$html);

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
