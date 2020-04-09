<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Storage;

class ConvertTest extends TestCase
{
    use RefreshDatabase;

    public function tearDown(): void
    {
        Storage::disk('public_uploads')->delete('file.ssml');

        parent::tearDown();
    }

    public function test_we_can_load_the_converter_form()
    {
        $response = $this->get('/converter');

        $response->assertStatus(200);
        $response->assertViewIs('converter');
    }

    public function test_it_can_convert_the_html_to_ssml()
    {
        $response = $this->withoutExceptionHandling()->post('/convert', [
            'name' => 'Some Name',
            'html' => $this->valid_html()]);

        //assert file was created
        $this->assertFileExists(\public_path('storage/some-name.ssml'));

        //assertContent is saved into the file
        $content = Storage::disk('public_uploads')->get('some-name.ssml');
        $this->assertEquals($this->valid_html(), $content);

        $response->assertRedirect('/');
        $response->assertSessionHas('message', 'Conversion Successful!');
        $response->assertSessionHas('link', 'Use this link to get the file: '.url('storage/some-name.ssml'));
        $this->assertDatabaseHas('ssmls', [
            'title' => 'Some Name',
            'link' => url('storage/some-name.ssml'),
            'content' => $this->valid_html(),
        ]);

    }

    /**
     * Valid HTML
     *
     * @return string
     */
    private function valid_html()
    {
        return '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu p</p>';
    }

}
