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
            ->find('//' . $where)
            ->append($tag);

        $this->html = preg_replace('/^.+\n/', '', (string) $html);

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

        $this->html = preg_replace('/^.+\n/', '', (string) $html);

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
