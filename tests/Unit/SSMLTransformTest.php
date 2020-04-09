<?php

namespace Tests\Unit;

use Tests\TestCase;
use Storage;
use App\SSMLTransformer;

class SSMLTransformTest extends TestCase
{

    public function test_it_can_remove_an_html_tag()
    {
        $html = '<div class="all"><p>Hey bro, <a href="google.com">click here</a><br /> :)</p></div>';
        $transformer = new SSMLTransformer($html);

        $transformer->removeTag('br');

        $this->assertFalse(\Str::contains($transformer->html, '<br />'));
    }

    public function test_we_can_add_an_html_tag()
    {
        $html = '<div class="all"><p>Hey bro, <a href="google.com">click here</a><br /> :)</p></div>';
        $transformer = new SSMLTransformer($html);

        $transformer->appendTag('<span></span>');

        $this->assertTrue(\Str::contains($transformer->html, 'span'));
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
