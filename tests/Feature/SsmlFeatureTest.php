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

    public function test_we_can_see_all_ssmls()
    {
        $ssml = factory(Ssml::class)->create([
            'title' => 'SSML',
            'link' => Ssml::getFilePath('some-file.ssml'),
        ]);

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
        $response = $this->post('/store', [
            'title' => 'Some Name',
            'html' => $this->valid_html(),
        ]);

        $response->assertRedirect('/')
            ->assertSessionHas('message', 'Conversion Successful!')
            ->assertSessionHas('link', 'Use this link to get the file: ' . url('storage/some-name.ssml'));

        $this->assertFileExists(\public_path('storage/some-name.ssml'));
        $this->assertFileExists(\public_path('readings/some-name.ssml.mp3'));
        $this->assertDatabaseHas('ssmls', [
            'title' => 'Some Name',
            'link' => url('storage/some-name.ssml'),
            'mp3' => url('readings/some-name.ssml.mp3'),
            'html' => $this->valid_html(),
            'content' => Storage::disk('public_uploads')->get('some-name.ssml'),
        ]);
    }

    public function test_we_can_delete_an_ssml()
    {
        \File::copy(base_path('tests/fixtures/reading.mp3'), public_path('readings/reading.mp3'));

        $filename = Ssml::getFilename('ssml file');
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
        $filename = Ssml::getFilename('ssml file');
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
        $existingFile = Ssml::getFilename('ssml file');
        $newFile = Ssml::getFilename('new ssml file');
        $ssml = Ssml::generate($this->valid_html(), $existingFile);
        $newSsml = Ssml::generate($this->new_html(), $newFile);

        $ssml = factory(Ssml::class)->create([
            'title' => 'SSML',
            'link' => Ssml::getFilePath($existingFile),
            'mp3' => url('readings/reading.mp3'),
            'html' => $this->valid_html(),
            'content' => $ssml->content,
        ]);

        $this->post('/ssml/' . $ssml->id, [
            'title' => 'New Title',
            'link' => Ssml::getFilePath($newFile),
            'html' => $this->new_html(),
        ])->assertRedirect('/ssml/' . $ssml->id);

        $this->assertFileNotExists(\public_path('storage/ssml-file.ssml'));
        $this->assertFileNotExists(\public_path('readings/reading.mp3'));
        $this->assertFileExists(\public_path('storage/new-title.ssml'));
        $this->assertFileExists(\public_path('readings/new-title.ssml.mp3'));
        $this->assertDatabaseHas('ssmls', [
            'id' => $ssml->id,
            'title' => 'New Title',
            'link' => Ssml::getFilePath(Ssml::getFilename('New Title')),
            'mp3' => url('readings/new-title.ssml.mp3'),
            'html' => $this->new_html(),
            'content' => $newSsml->content
        ]);
    }

    /**
     * Valid HTML
     *
     * @return string
     */
    private function valid_html()
    {
        return '<h2>Title</h2><p>Lore’m ipsum dolo’r <br /> sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus <br/> mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><img src="somefile.img" /><dl><dd>fejiafjeaw</dd><dt>feaf</dt></dl><figure></figure><table><thead><tr></tr></thead><tbody><tr><td></td></tr></tbody></table><p><strong>Some strong text</strong></p><p><em>Some Emphasis text</em></p><ul><li>some list text</li></ul>- —';
    }

    /**
     * Valid HTML
     *
     * @return string
     */
    private function valid_ssml()
    {
        return '<speak><p>Title</p><break time="1200ms"></break><p>Lore&apos;m ipsum dolo&apos;r  sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus  mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><break time="800ms"></break><p>fejiafjeaw</p><break time="800ms"></break><p>feaf</p><break time="800ms"></break><p>Some strong text</p><break time="800ms"><p>Some Emphasis text</p><break time="800ms"><p>some list text</p><break time="800ms"></break><p>Some strong text</p><p>Some Emphasis text</p>&ndash; &mdash;</speak>';
    }

    /**
     * Valid HTML
     *
     * @return string
     */
    private function new_html()
    {
        return '<h2>New Title</h2><p>Lore’m ipsum dolo’r <br /> sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus <br/> mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><img src="somefile.img" /><dl><dd>fejiafjeaw</dd><dt>feaf</dt></dl><figure></figure><table><thead><tr></tr></thead><tbody><tr><td></td></tr></tbody></table><p><strong>Some strong text</strong></p><p><em>Some Emphasis text</em></p><ul><li>some list text</li></ul>- —';
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
            ->removeTag('strong')
            ->removeTag('em')
            ->removeTag('table')
            ->appendTo('<break/>', 'p')
            ->appendAttr('break', ['time' => '800ms'])
            ->wrapAll('speak');

        $transformer->replaceApostrophes();
        $transformer->replaceDashes();
        $transformer->replaceLists();
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
            'link' => Ssml::getFilePath($filename),
            'mp3' => url('readings/reading.mp3'),
            'html' => $this->valid_html(),
            'content' => $transformer->content,
        ]);
    }

}
