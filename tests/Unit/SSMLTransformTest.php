<?php

namespace Tests\Unit;


use App\SSMLTransformer;
use Storage;
use Str;
use Tests\TestCase;
use function public_path;

class SSMLTransformTest extends TestCase
{
    public function test_it_can_replace_dash()
    {
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->replaceDashes();

        $this->assertStringNotContainsString('-', $transformer->content);
        $this->assertStringNotContainsString('—', $transformer->content);
    }

    public function test_it_can_replace_apostrophes()
    {
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->replaceApostrophes();

        $this->assertStringContainsString('&apos;', $transformer->content);
    }

    public function test_it_can_replace_quotes()
    {
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->replaceQuotes();

        $this->assertStringNotContainsString('“', $transformer->content);
        $this->assertStringNotContainsString('”', $transformer->content);
    }

    public function test_it_replaces_li_tags()
    {
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->replaceLists();

        $this->assertStringContainsString('<p>some list text</p><break time="800ms"></break>', $transformer->content);
    }

    public function test_it_replaces_strong_tags()
    {
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->replaceStrong();

        $this->assertStringNotContainsString('<strong>', $transformer->content);
        $this->assertStringNotContainsString('</strong>', $transformer->content);
        $this->assertStringContainsString('Some strong text', $transformer->content);
    }

    public function test_it_replaces_em_tags()
    {
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->replaceEmphasis();

        $this->assertStringNotContainsString('<em>', $transformer->content);
        $this->assertStringNotContainsString('</em>', $transformer->content);
        $this->assertStringContainsString('Some Emphasis text', $transformer->content);

    }

    public function it_can_remove_table_tags()
    {
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->removeTag('table');
        $this->assertStringNotContainsString('<table><thead><tr></tr></thead><tbody><tr><td></td></tr></tbody></table>', $transformer->content);
    }

    public function test_final_output_is_wrapped_in_speak_tags()
    {
        $html = '<p>Some Text</p>';

        $transformer = new SSMLTransformer($html);

        $transformer->wrapAll('speak');

        $this->assertEquals('<speak><p>Some Text</p></speak>', $transformer->content);
    }

    public function test_it_can_remove_an_html_tag()
    {
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->removeTag('br');

        $this->assertEquals(0, substr_count($transformer->content, '<br>'));
        $this->assertEquals(0, substr_count($transformer->content, '<br/>'));
    }

    public function test_it_can_remove_multiple_html_tags()
    {
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->removeTag('img')->removeTag('a');

        $this->assertEquals(0, substr_count($transformer->content, '<img src="#" alt="#"/>'));
    }

    public function test_we_are_removing_extra_elements()
    {
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->removeTag('br')
            ->removeTag('img')
            ->removeTag('dt')
            ->removeTag('dd')
            ->removeTag('figure')
            ->appendTo('<break/>', 'p')
            ->appendAttr('break', ['time' => '800ms']);

        $transformer->replaceHeaders('p');

        $transformer->save('some-name.ssml');

        $content = Storage::disk('public_uploads')->get('some-name.ssml');

        $this->assertEquals($transformer->content, $content);
        $this->assertEquals(0, substr_count($transformer->content, '<figure>'));
        $this->assertEquals(0, substr_count($transformer->content, '</figure>'));
        $this->assertEquals(0, substr_count($transformer->content, '<h2>'));
        $this->assertEquals(0, substr_count($transformer->content, '</h2>'));
        $this->assertEquals(0, substr_count($transformer->content, '<dt>'));
        $this->assertEquals(0, substr_count($transformer->content, '</dt>'));
        $this->assertEquals(0, substr_count($transformer->content, '<dd>'));
        $this->assertEquals(0, substr_count($transformer->content, '</dd>'));
        $this->assertEquals(0, substr_count($transformer->content, '<body>'));
        $this->assertEquals(0, substr_count($transformer->content, '</body>'));
        $this->assertEquals(0, substr_count($transformer->content, '<html>'));
        $this->assertEquals(0, substr_count($transformer->content, '</html>'));
    }

    public function test_we_can_append_an_html_tag()
    {
        $html = '<p>String</p>';
        $transformer = new SSMLTransformer($html);

        $transformer->appendTo('<break/>', 'p');

        $this->assertEquals('<p>String</p><break></break>', $transformer->content);
    }

    public function test_we_can_remove_glossary()
    {
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->removeTag('dt');
        $transformer->removeTag('dd');

        $this->assertEquals(0, substr_count($transformer->content, '<dt>'));
        $this->assertEquals(0, substr_count($transformer->content, '</dt>'));
        $this->assertEquals(0, substr_count($transformer->content, '<dd>'));
        $this->assertEquals(0, substr_count($transformer->content, '</dd>'));
    }

    public function test_we_can_replace_all_headers_tags()
    {
        $html = '<h2>Title</h2><h2>Another title</h2><p>Some text</p>';
        $transformer = new SSMLTransformer($html);

        $transformer->replaceHeaders('p');

        $this->assertEquals(3, substr_count($transformer->content, '<p>'));
        $this->assertEquals(3, substr_count($transformer->content, '</p>'));
    }

    public function test_we_can_replace_all_dl_remove_dt_tags()
    {
        $html = '<dl> <dt><strong style="font-size: 15px;">carrion</strong></dt> <dd style="margin-bottom: 10px;">Decaying animal flesh (meat).</dd> <dt><strong style="font-size: 15px;">invertebrate</strong></dt> <dd style="margin-bottom: 10px;">An animal without a backbone. Invertebrates include insects, worms, spiders, snails, crabs, and clams. </dd> <dt><strong style="font-size: 15px;">larvae</strong></dt> <dd style="margin-bottom: 10px;">Newly hatched insects with a different body structure from the adult.</dd> <dt><strong style="font-size: 15px;">naturalist</strong></dt> <dd style="margin-bottom: 10px;">A scientist who studies plants and animals in nature.</dd> <dt><strong style="font-size: 15px;">scavengers</strong></dt> <dd style="margin-bottom: 10px;">Animals that eat dead organisms they did not kill.</dd> </dl>';

        $transformer = new SSMLTransformer($html);

        $transformer->replaceGlossary();

        $this->assertEquals(10, substr_count($transformer->content, '<p>'));
        $this->assertEquals(0, substr_count($transformer->content, '<dl>'));
        $this->assertEquals(0, substr_count($transformer->content, '<dt>'));
        $this->assertEquals(0, substr_count($transformer->content, '<dd>'));
    }


    public function test_when_headers_are_replaced_break_and_time_attributes_uses()
    {
        $html = '<h2>Title</h2><h2>Another title</h2><p>Some text</p>';
        $transformer = new SSMLTransformer($html);

        $transformer->appendTo('<break/>', 'p')
            ->appendAttr('break', ['time' => '800ms']);

        $transformer->replaceHeaders('p');

        $this->assertEquals(3, substr_count($transformer->content, '<p>'));
        $this->assertEquals(3, substr_count($transformer->content, '</p>'));
        $this->assertEquals(1, substr_count($transformer->content, '<break time="800ms"></break>'));
        $this->assertEquals(2, substr_count($transformer->content, '<break time="1200ms"></break>'));
    }


    public function test_we_can_append_multiple_html_tags()
    {
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->appendTo('<span>Lorem</span>', 'p');

        $this->assertTrue(Str::contains($transformer->content, 'span'));
        $this->assertStringContainsString('<span>Lorem</span>', $transformer->content);
    }

    public function test_we_can_append_an_html_attribute()
    {
        $html = '<div class="all"><p>Hey bro, <a href="#">click here</a><br /> :)</p><p>Another Element</p></div>';
        $transformer = new SSMLTransformer($html);

        $transformer->appendAttr('p', ['class' => 'some-class']);

        $this->assertTrue(Str::contains($transformer->content, 'class="some-class"'));
    }

    public function test_we_can_append_multiple_ssml_break_tags_to_multiple_paragraphs()
    {
        $html = '<p>some text</p><p>more text</p>';

        $transformer = new SSMLTransformer($html);

        $transformer->appendTo('<break />', 'p')->appendAttr('break', ['time' => '800ms']);

        $this->assertEquals(2, substr_count($transformer->content, '<break time="800ms"></break>'));
    }

    public function test_it_can_save_a_file()
    {
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->removeTag('br')
            ->removeTag('img')
            ->appendTo('<break />', 'p')
            ->appendAttr('break', ['time' => '800ms'])
            ->save('file.ssml');

        $this->assertFileExists(public_path('storage/file.ssml'));
        $this->assertEquals($transformer->content, Storage::disk('public_uploads')->get('file.ssml'));
    }

    /**
     * Valid HTML
     *
     * @return string
     */
    private function valid_html()
    {
        return '<h2>Title</h2><p>Lore’m ipsum dolo’r <br /> sit amet, “consectetuer” adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus <br/> mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p><img src="somefile.img" /><dl><dd>fejiafjeaw</dd><dt>feaf</dt></dl><figure></figure><table><thead><tr></tr></thead><tbody><tr><td></td></tr></tbody></table><p><strong>Some strong text</strong></p><p><em>Some Emphasis text</em></p><ul><li>some list text</li></ul>- —';
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

    public function tearDown(): void
    {
        Storage::disk('public_uploads')->delete('file.ssml');

        parent::tearDown();
    }

}
