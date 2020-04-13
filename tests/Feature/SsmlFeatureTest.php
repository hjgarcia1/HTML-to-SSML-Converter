<?php

namespace Tests\Feature;

use App\Ssml;
use App\SSMLTransformer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Storage;
use Tests\TestCase;

class SsmlFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function tearDown(): void
    {
        Storage::disk('public_uploads')->delete('file.ssml');
        \File::delete(public_path('readings/some-name.ssml.mp3'));
        \File::delete(public_path('storage/some-name.ssml'));
        \File::delete(public_path('storage/new-file.ssml'));
        \File::delete(public_path('readings/new-title.ssml.mp3'));
        \File::delete(public_path('storage/new-title.ssml'));
        \File::delete(public_path('storage/ssml-file.ssml'));
        \File::delete(public_path('readings/some-name.ssml.mp3'));

        parent::tearDown();
    }
    public function test_we_can_all_ssmls()
    {
        $filename = $this->generateFilename('ssml file');
        $transformer = $this->generateSsmlFile($filename);
        $ssml = $this->createSsml($filename, $transformer);

        $this->get('/')
            ->assertStatus(200)
            ->assertViewIs('ssml.index')
            ->assertSee($ssml->id)
            ->assertSee($ssml->title)
            ->assertSee($ssml->link);
    }

    public function test_we_can_show_create_form()
    {
        $this->get('/create')->assertStatus(200)->assertViewIs('ssml.create');
    }

    public function test_we_can_save_an_ssml()
    {
        $filename = $this->generateFilename('ssml file');
        $transformer = $this->generateSsmlFile($filename);

        $response = $this->post('/store', [
            'title' => 'Some Name',
            'html' => $this->valid_html(),
        ]);


        $content = Storage::disk('public_uploads')->get('some-name.ssml');

        $response->assertRedirect('/')
            ->assertSessionHas('message', 'Conversion Successful!')
            ->assertSessionHas('link', 'Use this link to get the file: ' . url('storage/some-name.ssml'));

        $this->assertDatabaseHas('ssmls', [
            'title' => 'Some Name',
            'link' => url('storage/some-name.ssml'),
            'mp3' => url('readings/some-name.ssml.mp3'),
            'html' => $this->valid_html(),
            'content' => $transformer->content,
        ]);

        $this->assertFileExists(\public_path('storage/some-name.ssml'));
        $this->assertFileExists(\public_path('readings/some-name.ssml.mp3'));
        $this->assertEquals($this->valid_ssml(), $content);
    }

    public function test_we_can_delete_an_ssml()
    {
        \File::copy(base_path('tests/fixtures/reading.mp3'), public_path('readings/reading.mp3'));

        $filename = $this->generateFilename('ssml file');
        $transformer = $this->generateSsmlFile($filename);
        $ssml = $this->createSsml($filename, $transformer);

        $response = $this->delete('/ssml/' . $ssml->id);

        $response->assertRedirect('/');
        $response->assertSessionHas('message', 'SSML file was deleted!');
        $this->assertDatabaseMissing('ssmls', [ 'id' => $ssml->id]);
        $this->assertFileNotExists(\public_path('storage/ssml-file.ssml'));
        $this->assertFileNotExists(\public_path('readings/reading.mp3'));
    }

    public function test_we_can_edit_an_ssml()
    {
        $filename = $this->generateFilename('ssml file');
        $transformer = $this->generateSsmlFile($filename);
        $ssml = $this->createSsml($filename, $transformer);

        $this->get('/ssml/' . $ssml->id)
            ->assertOk()
            ->assertSee($ssml->title)
            ->assertSee($ssml->link)
            ->assertSee($ssml->html);
    }

    public function test_we_can_update_an_ssml()
    {
        \File::copy(base_path('tests/fixtures/reading.mp3'), public_path('readings/reading.mp3'));
        //existing ssml transformation
        $transformer = new SSMLTransformer($this->valid_html());
        $existingFile = $this->generateFilename('ssml file');
        $transformer->removeTag('br')
            ->removeTag('img')
            ->appendTo('<break/>', 'h2')
            ->appendTo('<break/>', 'p')
            ->appendAttr('break', ['time' => '800ms'])
            ->wrapAll('speak');

        $transformer->replaceGlossary()->replaceHeaders('p');

        $transformer->save($existingFile);

        //new ssml transformation
        $newTransformer = new SSMLTransformer($this->new_html());
        $newFile = $this->generateFilename('new file');
        $newTransformer->removeTag('br')
            ->removeTag('img')
            ->appendTo('<break/>', 'h2')
            ->appendTo('<break/>', 'p')
            ->appendAttr('break', ['time' => '800ms'])
            ->wrapAll('speak');

        $newTransformer->replaceGlossary()->replaceHeaders('p');

        $newTransformer->save($existingFile);


        $ssml = factory(Ssml::class)->create([
            'title' => 'SSML',
            'link' => $this->getFilePath($existingFile),
            'mp3' => url('readings/reading.mp3'),
            'html' => $this->valid_html(),
            'content' => $transformer->content,
        ]);

        $response = $this->post('/ssml/' . $ssml->id, [
            'title' => 'New Title',
            'link' => $this->getFilePath($newFile),
            'html' => $this->new_html(),
        ]);

        $response->assertRedirect('/ssml/' . $ssml->id);
        $this->assertDatabaseHas('ssmls', [
            'id' => $ssml->id,
            'title' => 'New Title',
            'link' => $this->getFilePath($this->generateFilename('New Title')),
            'mp3' => url('readings/new-title.ssml.mp3'),
            'html' => $this->new_html(),
//            'content' => $newTransformer->content,
        ]);
        $this->assertFileNotExists(\public_path('storage/ssml-file.ssml'));
        $this->assertFileExists(\public_path('readings/new-title.ssml.mp3'));
        $this->assertFileNotExists(\public_path('readings/reading.mp3'));
        $this->assertFileExists(\public_path('storage/new-title.ssml'));
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
     * @param string $filename
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|string
     */
    protected function getFilePath(string $filename)
    {
        return url('storage/' . $filename);
    }

    /**
     * Valid HTML
     *
     * @return string
     */
    private function valid_html()
    {
        return '<h2>Title</h2><p>Lorem ipsum dolor <br /> sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus <br/> mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><img src="somefile.img" /><dl><dd>fejiafjeaw</dd><dt>feaf</dt></dl><figure></figure>';
    }

    /**
     * Valid HTML
     *
     * @return string
     */
    private function valid_ssml()
    {
        return '<speak><p>Title</p><break time="1200ms"></break><p>Lorem ipsum dolor  sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus  mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><break time="800ms"></break><p>fejiafjeaw</p><break time="800ms"></break><p>feaf</p></speak>';
    }

    /**
     * Valid HTML
     *
     * @return string
     */
    private function new_html()
    {
        return '<h2>Title</h2><p>Lorem ipsum dolor <br /> sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus <br/> mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><img src="somefile.img" /><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu</p><dl><dd>fejiafjeaw</dd><dt>feaf</dt></dl><figure></figure>';
    }

    /**
     * @param string $filename
     * @return SSMLTransformer
     */
    protected function generateSsmlFile(string $filename)
    {
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->removeTag('br')
            ->removeTag('figure')
            ->removeTag('img')
            ->appendTo('<break/>', 'p')
            ->appendAttr('break', ['time' => '800ms'])
            ->wrapAll('speak');

        $transformer->replaceGlossary();
        $transformer->replaceHeaders('p');

        $transformer->save($filename);

        return $transformer;
    }

    /**
     * @param string $filename
     * @param SSMLTransformer $transformer
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    protected function createSsml(string $filename, SSMLTransformer $transformer)
    {
        return factory(Ssml::class)->create([
            'title' => 'SSML',
            'link' => $this->getFilePath($filename),
            'mp3' => url('readings/reading.mp3'),
            'html' => $this->valid_html(),
            'content' => $transformer->content,
        ]);
    }

}
