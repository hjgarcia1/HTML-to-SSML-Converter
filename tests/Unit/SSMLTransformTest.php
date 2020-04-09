<?php

namespace Tests\Unit;


use App\SSMLTransformer;
use Storage;
use Tests\TestCase;

class SSMLTransformTest extends TestCase
{

    public function test_it_can_remove_an_html_tag()
    {
        $html = '<div class="all"><p>Hey bro, <a href="google.com">click here</a><br /> :)</p></div>';

        $transformer = new SSMLTransformer($html);

        $transformer->removeTag('br');

        $this->assertEquals(0, substr_count($transformer->html, '<br />'));
    }

    public function test_it_can_remove_multiple_html_tags()
    {
        $html = '<div class="all"><p>Hey bro, <a href="google.com">click here</a><img src="google.com"/><img src="google.com"/></p></div>';

        $transformer = new SSMLTransformer($html);

        $this->assertEquals(2, substr_count($transformer->html, '<img src="google.com"/>'));

        $transformer->removeTag('img');

        $this->assertEquals(0, substr_count($transformer->html, '<img src="google.com"/>'));
    }

    public function test_we_can_add_an_html_tag()
    {
        $html = '<div class="all"><p>Hey bro, <a href="google.com">click here</a><br /> :)</p></div>';
        $transformer = new SSMLTransformer($html);

        $transformer->appendTo('<span>Lorem</span>', 'p');

        $this->assertTrue(\Str::contains($transformer->html, 'span'));
    }

    public function test_we_append_multiple_html_tags()
    {
        $html = '<div class="all"><p>Hey bro, <a href="google.com">click here</a><br /> :)</p><p>Another Element</p></div>';
        $transformer = new SSMLTransformer($html);

        $transformer->appendTo('<span>Lorem</span>', 'p');

        $this->assertTrue(\Str::contains($transformer->html, 'span'));
        $this->assertEquals(2, substr_count($transformer->html, '<span>'));
        $this->assertEquals(2, substr_count($transformer->html, '</span>'));
    }

    public function test_it_can_save_a_file()
    {
        $transformer = new SSMLTransformer();

        $transformer->save('html', 'some-name.ssml');

        $this->assertFileExists(\public_path('storage/some-name.ssml'));
        //assertContent is saved into the file
        $content = Storage::disk('public_uploads')->get('some-name.ssml');
        $this->assertEquals('html', $content);
    }
}
