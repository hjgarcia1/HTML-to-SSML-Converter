<?php

namespace Tests\Unit;


use App\SSMLTransformer;
use Storage;
use Str;
use Tests\TestCase;
use function public_path;

class SSMLTransformTest extends TestCase
{

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
            ->replaceHeaders('p')
            ->appendTo('<break/>', 'p')
            ->appendAttr('break', ['time' => '800ms'])
            ->save('some-name.ssml');


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

    public function test_when_headers_are_replaced_break_and_time_attributes_uses()
    {
        $html = '<h2>Title</h2><h2>Another title</h2><p>Some text</p>';
        $transformer = new SSMLTransformer($html);

        $transformer->replaceHeaders('p')
            ->appendTo('<break/>', 'p')
            ->appendAttr('break', ['time' => '800ms']);

        $this->assertEquals(3, substr_count($transformer->content, '<p>'));
        $this->assertEquals(3, substr_count($transformer->content, '</p>'));
        $this->assertEquals(3, substr_count($transformer->content, '<break time="800ms"></break>'));
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
        //assertContent is saved into the file
        $content = Storage::disk('public_uploads')->get('file.ssml');

        $this->assertStringContainsString('<break time="800ms"></break>', $content);
    }

    protected function valid_html()
    {
        return '<h2>Meerkat</h2>  <br><figure><img          src="https://s3.amazonaws.com/s3-static.iwqst.com/assets/media/al-prime/plants-and-animals/readings/meerkat/AnimalStories_Meerkat_lores-01.jpg"          alt="Meerkat" width="650"></figure>  <figure><img          src="https://s3.amazonaws.com/s3-static.iwqst.com/assets/media/al-prime/plants-and-animals/readings/meerkat/AnimalStories_Meerkat_lores-02.jpg"          alt="Meerkats" width="650"></figure>  <p>Meerkats live in dry deserts or grasslands. They live in groups with other meerkats.</p>  <figure><img          src="https://s3.amazonaws.com/s3-static.iwqst.com/assets/media/al-prime/plants-and-animals/readings/meerkat/AnimalStories_Meerkat_lores-03.jpg"          alt="Meerkats" width="650"></figure>  <p>Meerkats take turns standing guard. They watch for bigger animals. This helps keep the group safe.</p>  <figure><img          src="https://s3.amazonaws.com/s3-static.iwqst.com/assets/media/al-prime/plants-and-animals/readings/meerkat/AnimalStories_Meerkat_lores-04.jpg"          alt="Meerkats" width="650"></figure>  <p>Then other meerkats in the group can eat, rest, or play.</p>  <figure><img          src="https://s3.amazonaws.com/s3-static.iwqst.com/assets/media/al-prime/plants-and-animals/readings/meerkat/AnimalStories_Meerkat_lores-05.jpg"          alt="Meerkats" width="650"></figure>  <p>Meerkats eat insects and other small animals. They also eat plants. This meerkat is eating a grasshopper.</p>  <figure><img          src="https://s3.amazonaws.com/s3-static.iwqst.com/assets/media/al-prime/plants-and-animals/readings/meerkat/AnimalStories_Meerkat_lores-06.jpg"          alt="Meerkats" width="650"></figure>  <p>Meerkats get water wherever they can find it. This meerkat is drinking from a tiny puddle.</p>  <figure><img          src="https://s3.amazonaws.com/s3-static.iwqst.com/assets/media/al-prime/plants-and-animals/readings/meerkat/AnimalStories_Meerkat_lores-07.jpg"          alt="Meerkats" width="650"></figure>  <p>Meerkats make their homes by digging into the ground.</p>  <p>Their underground homes are called burrows.</p>  <p>Burrows give them shade and a safe place to sleep.</p>  <figure><img          src="https://s3.amazonaws.com/s3-static.iwqst.com/assets/media/al-prime/plants-and-animals/readings/meerkat/AnimalStories_Meerkat_lores-08.jpg"          alt="Meerkats" width="650"></figure>  <p>Meerkats find everything they need in the places where they live.</p>';
    }

    public function tearDown(): void
    {
        Storage::disk('public_uploads')->delete('file.ssml');

        parent::tearDown();
    }

}
