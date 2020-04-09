<?php

namespace Tests\Unit;


use App\SSMLTransformer;
use Storage;
use Tests\TestCase;

class SSMLTransformTest extends TestCase
{

    public function test_it_can_remove_an_html_tag()
    {
        $html = '<div class="all"><p>Hey bro, <a href="#">click here</a><br /> :)</p></div>';

        $transformer = new SSMLTransformer($html);

        $transformer->removeTag('br');

        $this->assertEquals(0, substr_count($transformer->html, '<br />'));
    }

    public function test_it_can_remove_multiple_html_tags()
    {
        $html = '<div class="all"><p>Hey bro, <a href="#">click here</a><img src="#"/><img src="#"/></p></div>';

        $transformer = new SSMLTransformer($html);

        $this->assertEquals(2, substr_count($transformer->html, '<img src="#"/>'));

        $transformer->removeTag('img');

        $this->assertEquals(0, substr_count($transformer->html, '<img src="#"/>'));
    }

    public function test_we_can_append_an_html_tag()
    {
        $html = '<div class="all"><p>Hey bro, <a href="#">click here</a><br /> :)</p><p>fejaf</p></div>';
        $transformer = new SSMLTransformer($html);

        $transformer->appendTo('<break />', 'p');

        $this->assertEquals(2, substr_count($transformer->html, '<break />'));
    }

    public function test_we_can_append_multiple_html_tags()
    {
        $html = '<div class="all"><p>Hey bro, <a href="#">click here</a><br /> :)</p><p>Another Element</p></div>';
        $transformer = new SSMLTransformer($html);

        $transformer->appendTo('<span>Lorem</span>', 'p');

        $this->assertTrue(\Str::contains($transformer->html, 'span'));
        $this->assertEquals(2, substr_count($transformer->html, '<span>'));
        $this->assertEquals(2, substr_count($transformer->html, '</span>'));
    }

    public function test_we_can_append_an_html_attribute()
    {
        $html = '<div class="all"><p>Hey bro, <a href="#">click here</a><br /> :)</p><p>Another Element</p></div>';
        $transformer = new SSMLTransformer($html);

        $transformer->appendAttr('p',['class' => 'some-class']);

        $this->assertTrue(\Str::contains($transformer->html, 'class="some-class"'));
    }

    public function test_we_can_append_multiple_ssml_break_tags_to_multiple_paragraphs()
    {
        $html = '<div><p>some text</p><p>more text</p></div>';

        $transformer = new SSMLTransformer($html);

        $transformer->appendTo('<break />', 'p')->appendAttr('break', ['time' => '800ms']);

        $this->assertEquals(2, substr_count($transformer->html, '<break time="800ms"/>'));;
    }

    public function test_it_can_save_a_file()
    {
        $transformer = new SSMLTransformer('html');

        $transformer->save('html', 'some-name.ssml');

        $this->assertFileExists(\public_path('storage/some-name.ssml'));
        //assertContent is saved into the file
        $content = Storage::disk('public_uploads')->get('some-name.ssml');
        $this->assertEquals('html', $content);
    }
}
