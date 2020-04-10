<?php

namespace Tests\Feature;

use App\Ssml;
use App\SSMLTransformer;
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
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->removeTag('br')
            ->removeTag('img')
            ->appendTo('<break/>', 'h2')
            ->appendTo('<break/>', 'p')
            ->appendAttr('break', ['time' => '800ms']);

        $response = $this->post('/convert', [
            'name' => 'Some Name',
            'html' => $this->valid_html()]);

        //assert file was created
        $this->assertFileExists(\public_path('storage/some-name.ssml'));

        //assertContent is saved into the file
        $content = Storage::disk('public_uploads')->get('some-name.ssml');
        $this->assertEquals($transformer->content, $content);

        $response->assertRedirect('/');
        $response->assertSessionHas('message', 'Conversion Successful!');
        $response->assertSessionHas('link', 'Use this link to get the file: ' . url('storage/some-name.ssml'));
        $this->assertDatabaseHas('ssmls', [
            'title' => 'Some Name',
            'link' => url('storage/some-name.ssml'),
            'content' => $transformer->content,
        ]);
        $this->assertStringNotContainsString('<br />', $content);
        $this->assertStringNotContainsString('<img src="somefile.img" />', $content);
    }

    public function test_we_can_delete_an_ssml_file_along_with_database_record()
    {
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->removeTag('br')
            ->removeTag('img')
            ->appendTo('<break/>', 'h2')
            ->appendTo('<break/>', 'p')
            ->appendAttr('break', ['time' => '800ms']);

        $ssml = factory(Ssml::class)->create([
            'title' => 'SSML',
            'link' => $this->generateFilename('SSML'),
            'content' => $transformer->content,
        ]);

        $response = $this->get('/ssml/' . $ssml->id);

        $response->assertOk();
        $response->assertSee($ssml->title);
        $response->assertSee($ssml->link);
    }

    /**
     * @param $name
     * @return string
     */
    protected function generateFilename($name): string
    {
        return \Str::slug($name) . '.ssml';
    }

    /**
     * Valid HTML
     *
     * @return string
     */
    private function valid_html()
    {
        return '<h2>Title</h2><p>Lorem ipsum dolor <br /> sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus <br/> mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><img src="somefile.img" /><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu</p>';
    }

}
