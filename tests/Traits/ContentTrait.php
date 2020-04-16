<?php
namespace Tests\Traits;

use App\Ssml;
use App\SSMLFileTransformer;

trait ContentTrait
{
    /**
     * Valid HTML
     *
     * @return string
     */
    function valid_html()
    {
        return '<h2>People Doing Science</h2><h3>The Garbage Project</h3><p>Lore’m ipsum dolo’r <br /> sit amet, “consectetuer” adipiscing elit. Aenean commodo ligula eget dolor. Aenean—massa. Cum-sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus <br/> mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><img src="somefile.img" /><dl><dd>fejiafjeaw</dd><dt>feaf</dt></dl><figure></figure><table><thead><tr></tr></thead><tbody><tr><td></td></tr></tbody></table><p><strong>Some strong text</strong></p><p><em>Some Emphasis text</em></p><ul><li>some list text</li></ul>';
    }

    /**
     * Valid HTML
     *
     * @return string
     */
    function valid_ssml()
    {
        return '<speak><p>People Doing Science</p><break time="1200ms"></break><p>The Garbage Project</p><break time="1200ms"></break><p>Lore\'m ipsum dolo\'r  sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean<break time="500ms"></break>massa. Cum<break time="500ms"></break>sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus  mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><break time="800ms"></break><p>fejiafjeaw</p><break time="800ms"></break><p>feaf</p><break time="800ms"></break><p>Some strong text</p><break time="800ms"></break><p>Some Emphasis text</p><break time="800ms"></break><p>some list text</p><break time="800ms"></break></speak>';
    }

    /**
     * Valid HTML
     *
     * @return string
     */
    function new_html()
    {
        return '<h2>New Title</h2><p>Lore’m ipsum dolo’r <br /> sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus <br/> mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><img src="somefile.img" /><dl><dd>fejiafjeaw</dd><dt>feaf</dt></dl><figure></figure><table><thead><tr></tr></thead><tbody><tr><td></td></tr></tbody></table><p><strong>Some strong text</strong></p><p><em>Some Emphasis text</em></p><ul><li>some list text</li></ul>- —';
    }

    /**
     * @param string $filename
     * @return SSMLFileTransformer
     */
    function generateSsmlFile(string $filename)
    {
        $transformer = new SSMLFileTransformer($this->valid_html());

        $transformer->removeTag('br')
            ->removeTag('figure')
            ->removeTag('img')
            ->removeTag('strong')
            ->removeTag('em')
            ->removeTag('table')
            ->appendTo('<break/>', 'p')
            ->appendAttr('break', ['time' => '800ms'])
            ->wrapAll('speak');

        $transformer->replaceApostrophes();
        $transformer->replaceDashes();
        $transformer->replaceLists();
        $transformer->replaceGlossary();
        $transformer->replaceHeaders();

        $transformer->save($filename);

        return $transformer;
    }

    /**
     * @param string $filename
     * @param SSMLFileTransformer $transformer
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    function createSsml(string $filename, SSMLFileTransformer $transformer)
    {
        return factory(Ssml::class)->create([
            'title' => 'SSML',
            'link' => Ssml::getFilePath($filename),
            'mp3' => url('readings/reading.mp3'),
            'html' => $this->valid_html(),
            'content' => $transformer->content,
        ]);
    }
}
