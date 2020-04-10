<?php

namespace Tests\Unit;


use App\SSMLTransformer;
use Storage;
use Str;
use Tests\TestCase;
use function public_path;

class SSMLTransformTest extends TestCase
{

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

    public function test_we_can_remove_all_headers_tags()
    {
        $transformer = new SSMLTransformer($this->valid_html());

        $transformer->removeTag('h2');

        $this->assertEquals(0, substr_count($transformer->content, '<h2>'));
        $this->assertEquals(0, substr_count($transformer->content, '</h2>'));
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
        return '<h2>What We See in the Sky</h2>    <img style= "padding-right: 21px" src="#" alt="#"/>    <img src="#" alt="#"/>    <img src="#" alt="#"/>    <p>Look up at the sky. What can you see in the sky?</p>  <p>You might see some flying birds. You might see a plane. You might even see a rainbow.</p>  <p>These objects are in the air above us.</p>    <img style= "padding-right: 4px" src="#" alt="#"/>    <img src="#" alt="#"/>    <p>During daytime, you see the <strong>sun</strong>. Sometimes you can see it clearly. At other times clouds block the sun, but we can still see some of its light.</p>    <img src="#" alt="#"/>    <p>During daytime, the sun provides heat and light to the earth.</p>    <img src="#" alt="#"/>    <img src="#" alt="#"/>    <p>If the sky is clear at night, you may see <strong>stars</strong>. Stars are huge balls of gas. They are far away in space, but we can see their light.</p>    <p>On a clear night, you may also see the <strong>moon</strong>.</p>    <img src="#" alt="#"/>    <p>We can see the moon during both the day and the night. This is the moon during the day.</p>    <img src="#" alt="#"/>    <p>Have you ever seen the moon during the day?</p>    <img src="#" alt="#"/>    <p>The sun, moon, and stars are called <strong>celestial objects</strong>. A celestial object is a natural object that we can see even though it is far away.</p>    <img src="#" alt="#"/>    <p>Scientists look for <strong>patterns</strong> as they observe the sky and ask questions.</p>  <p>What do you see when you look up at the sky?</p>  <br/>  <br/>    <h2>Glossary</h2>      <dl>    	<div style="display: grid; grid-gap: 10px; margin-bottom: 20px">            		<dt style="margin-right: 10px;"><strong>Celestial object</strong></dt>            		<dd>Any natural object that appears far away in our sky, such as the sun, moon, and stars.</dd>        	</div>          	<div style="display: grid; grid-gap: 10px; margin-bottom: 20px">            		<dt style="margin-right: 10px;"><strong>Moon</strong></dt>            		<dd>The object that circles Earth and shines by light reflected from the sun.</dd>        	</div>          	<div style="display: grid; grid-gap: 10px; margin-bottom: 20px">            		<dt style="margin-right: 10px;"><strong>Sun</strong></dt>      		<dd>The object in space that provides heat and light to Earth. Earth travels around the sun.</dd>        	</div>        	  	<div style="display: grid; grid-gap: 10px; margin-bottom: 20px">            		<dt style="margin-right: 10px;"><strong>Star</strong></dt>            		<dd>A huge ball of gas. Stars gives off heat and light and appear as bright points in the night sky because most of them are so far away. The star that is closest to Earth is the sun.</dd>        	</div>          </dl>	                                                              ';
    }

    public function tearDown(): void
    {
        Storage::disk('public_uploads')->delete('file.ssml');

        parent::tearDown();
    }

}
