<?php

namespace Tests\Unit;


use App\Ssml;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SSMLTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_a_title_field()
    {
        $ssml = factory(Ssml::class)->create(['title' => 'Some Title']);

        $this->assertEquals('Some Title', $ssml->fresh()->title);
    }

    public function test_it_has_a_link_field()
    {
        $ssml = factory(Ssml::class)->create(['link' => 'some link']);

        $this->assertEquals('some link', $ssml->fresh()->link);
    }

    public function test_it_has_an_mp3_field()
    {
        $ssml = factory(Ssml::class)->create(['mp3' => 'mp3']);

        $this->assertEquals('mp3', $ssml->fresh()->mp3);
    }

    public function test_it_has_an_html_field()
    {
        $ssml = factory(Ssml::class)->create(['html' => 'some html']);

        $this->assertEquals('some html', $ssml->fresh()->html);
    }

    public function test_it_has_a_content_field()
    {
        $ssml = factory(Ssml::class)->create(['content' => 'some content']);

        $this->assertEquals('some content', $ssml->fresh()->content);
    }

    public function test_it_can_get_a_file_name()
    {
        $filename = Ssml::getFilename('Some File');

        $this->assertEquals('some-file.ssml', $filename);
    }

    public function test_it_can_get_a_file_path()
    {
        $path = Ssml::getFilePath(Ssml::getFilename('Some File'));

        $this->assertEquals(url('storage/some-file.ssml'), $path);
    }
}
