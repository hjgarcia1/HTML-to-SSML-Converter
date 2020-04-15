<?php

namespace Tests\Unit;


use App\Ssml;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Storage;
use Tests\TestCase;
use Tests\Traits\ContentTrait;

class SSMLTest extends TestCase
{
    use RefreshDatabase, ContentTrait;

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

    public function test_it_can_generate_an_ssml_file()
    {
        $ssml = Ssml::generate($this->valid_html(), 'some-file.ssml');

        $fileContent = Storage::disk('public_uploads')->get('some-file.ssml');

        $this->assertFileExists(public_path('storage/some-file.ssml'));
        $this->assertEquals($this->valid_ssml(), $fileContent);
    }
}
