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

        parent::tearDown();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_it_loads_the_form()
    {
        $ssmls = factory(Ssml::class, 20)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('ssml.index');
        $ssmls->each(function ($ssml) use ($response) {
            $response->assertSee($ssml->id);
            $response->assertSee($ssml->title);
            $response->assertSee($ssml->link);
        });
    }


    public function test_we_can_show_create_form()
    {
        $response = $this->get('/create');

        $response->assertStatus(200);
        $response->assertViewIs('ssml.create');
    }

    public function test_we_can_save_an_ssml()
    {
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->removeTag('br')
            ->removeTag('img')
            ->removeTag('dt')
            ->removeTag('dd')
            ->removeTag('figure')
            ->appendTo('<break/>', 'p')
            ->appendAttr('break', ['time' => '800ms'])
            ->wrapAll('speak');

        $transformer->replaceHeaders('p');

        $response = $this->withoutExceptionHandling()->post('/store', [
            'title' => 'Some Name',
            'html' => $this->valid_html()
        ]);

        //assert file was created
        $this->assertFileExists(\public_path('storage/some-name.ssml'));
        //assert mp3 was created
        $this->assertFileExists(\public_path('readings/some-name.ssml.mp3'));

        //assertContent is saved into the file
        $content = Storage::disk('public_uploads')->get('some-name.ssml');
        $this->assertEquals($this->valid_ssml(), $content);

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

    public function test_we_can_delete_an_ssml()
    {
        $transformer = new SSMLTransformer($this->valid_html());
        $filename = $this->generateFilename('ssml file');

        $transformer->removeTag('br')
            ->removeTag('img')
            ->appendTo('<break/>', 'h2')
            ->appendTo('<break/>', 'p')
            ->appendAttr('break', ['time' => '800ms'])
            ->save($filename);

        $ssml = factory(Ssml::class)->create([
            'title' => 'SSML',
            'link' => $this->getFilePath($filename),
            'html' => $this->valid_html(),
            'content' => $transformer->content,
        ]);

        $response = $this->delete('/ssml/' . $ssml->id);

        $response->assertRedirect('/');
        $response->assertSessionHas('message', 'SSML file was deleted!');
        $this->assertDatabaseMissing('ssmls', [
            'title' => 'SSML',
            'link' => $this->getFilePath($filename),
            'html' => $this->valid_html(),
            'content' => $transformer->content,
        ]);
        //assert file was created
        $this->assertFileNotExists(\public_path('storage/ssml-file.ssml'));
    }

    public function test_we_can_edit_an_ssml()
    {
        $transformer = new SSMLTransformer($this->valid_html());
        $filename = $this->generateFilename('ssml file');

        $transformer->removeTag('br')
            ->removeTag('img')
            ->appendTo('<break/>', 'h2')
            ->appendTo('<break/>', 'p')
            ->appendAttr('break', ['time' => '800ms'])
            ->save($filename);

        $ssml = factory(Ssml::class)->create([
            'title' => 'SSML',
            'link' => $this->getFilePath($filename),
            'html' => $this->valid_html(),
            'content' => $transformer->content,
        ]);

        $response = $this->get('/ssml/' . $ssml->id);

        $response->assertOk();
        $response->assertSee($ssml->title);
        $response->assertSee($ssml->link);
        $response->assertSee($ssml->html);
    }

    public function test_we_can_update_an_ssml()
    {
        //existing ssml transformation
        $transformer = new SSMLTransformer($this->valid_html());
        $existingFile = $this->generateFilename('ssml file');
        $transformer->removeTag('br')
            ->removeTag('img')
            ->replaceHeaders('p')
            ->appendTo('<break/>', 'h2')
            ->appendTo('<break/>', 'p')
            ->appendAttr('break', ['time' => '800ms'])
            ->wrapAll('speak')
            ->save($existingFile);

        //new ssml transformation
        $newTransformer = new SSMLTransformer($this->new_html());
        $newFile = $this->generateFilename('new file');
        $newTransformer->removeTag('br')
            ->removeTag('img')
            ->replaceHeaders('p')
            ->appendTo('<break/>', 'p')
            ->appendAttr('break', ['time' => '800ms'])
            ->wrapAll('speak')
            ->save($newFile);


        $ssml = factory(Ssml::class)->create([
            'title' => 'SSML',
            'link' => $this->getFilePath($existingFile),
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
            'html' => $this->new_html(),
//            'content' => $newTransformer->content,
        ]);
        $this->assertFileNotExists(\public_path('storage/ssml-file.ssml'));
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
        return '<h2>Title</h2><p>Lorem ipsum dolor <br /> sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus <br/> mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><img src="somefile.img" /><dd><dt>feaf</dt></dd><figure></figure>';
    }

    /**
     * Valid HTML
     *
     * @return string
     */
    private function valid_ssml()
    {
        return '<speak><p>Title</p><break time="1200ms"></break><p>Lorem ipsum dolor  sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus  mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><break time="800ms"></break></speak>';
    }

    /**
     * Valid HTML
     *
     * @return string
     */
    private function new_html()
    {
        return '<h2>Title</h2><p>Lorem ipsum dolor <br /> sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus <br/> mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><img src="somefile.img" /><p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu</p><dd><dt>feaf</dt></dd><figure></figure>';
    }

}
