<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Storage;

class ValidationTest extends TestCase
{

    public function test_it_the_name_field_required()
    {
        $response = $this->post('/convert', [
            'name' => '',
            'html' => $this->valid_html()
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('name');
    }

    public function test_html_field_is_required()
    {
        $response = $this->post('/convert', [
            'name' => 'some name',
            'html' => ''
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('html');
    }

    public function test_html_field_is_cannot_be_more_than_500_characters()
    {
        $response = $this->post('/convert', [
            'name' => 'some name',
            'html' => $this->invalid_html()
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('html');
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

    /**
     * Invalid HTML
     *
     * @return string
     */
    private function invalid_html()
    {
        return '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu,</p>';
    }
}
