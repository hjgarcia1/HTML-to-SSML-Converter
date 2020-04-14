<?php

namespace App;

/**
 * Class Ssml
 * @package App
 */
class Ssml extends \Eloquent
{
    protected $table = 'ssmls';

    protected $fillable = [
        'title',
        'link',
        'mp3',
        'html',
        'content'
    ];

    /**
     * Get the filename for an Ssml file name
     *
     * @param $name
     * @return string
     */
    public static function getFilename($name): string
    {
        return \Str::slug($name) . '.ssml';
    }

    /**
     * Get the file path for an Ssml
     *
     * @param string $filename
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public static function getFilePath(string $filename)
    {
        return url('storage/' . $filename);
    }

    /**
     * Generate Ssml output and save
     *
     * @param $html
     * @param string $filename
     * @return SSMLTransformer
     */
    public static function generate($html, string $filename): SSMLTransformer
    {
        $ssml = new SSMLTransformer($html);

        $ssml
            ->removeTag('br')
            ->removeTag('table')
            ->removeTag('img')
            ->removeTag('figure')
            ->appendTo('<break />', 'p')
            ->appendAttr('break', ['time' => '800ms'])
            ->wrapAll('speak');

        $ssml->replaceHeaders('p')->replaceGlossary();

        $ssml->save($filename);

        return $ssml;
    }
}
