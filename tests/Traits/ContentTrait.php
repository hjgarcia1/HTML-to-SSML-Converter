<?php
namespace Tests\Traits;

trait ContentTrait
{
    /**
     * Valid HTML
     *
     * @return string
     */
    function valid_html()
    {
        return '<h2>Title</h2><p>Lore’m ipsum dolo’r <br /> sit amet, “consectetuer” adipiscing elit. Aenean commodo ligula eget dolor. Aenean—massa. Cum-sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus <br/> mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><img src="somefile.img" /><dl><dd>fejiafjeaw</dd><dt>feaf</dt></dl><figure></figure><table><thead><tr></tr></thead><tbody><tr><td></td></tr></tbody></table><p><strong>Some strong text</strong></p><p><em>Some Emphasis text</em></p><ul><li>some list text</li></ul>';
    }

    /**
     * Valid HTML
     *
     * @return string
     */
    function valid_ssml()
    {
        return '<speak><p>Title</p><break time="1200ms"></break><p>Lore&apos;m ipsum dolo&apos;r  sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean<break time="500ms"></break>massa. Cum<break time="500ms"></break>sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus  mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><break time="800ms"></break><p>fejiafjeaw</p><break time="800ms"></break><p>feaf</p><break time="800ms"></break><p>Some strong text</p><break time="800ms"></break><p>Some Emphasis text</p><break time="800ms"></break><p>some list text</p><break time="800ms"></break></speak>';
    }
}
