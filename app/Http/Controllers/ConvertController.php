<?php

namespace App\Http\Controllers;

use Storage;

class ConvertController extends Controller
{
    public function transform()
    {
        $data = request()->validate([
            'name' => 'required',
            'html' => 'required|max:500',
        ]);

        //set the file name
        $filename = \Str::slug($data['name']);

        //create the file
        Storage::disk('public_uploads')->put($filename . '.ssml', $data['html']);

        //redirect back to the home page with message
        return redirect('/')
            ->with('link', 'Use this link to get the file: ' . url('storage/' . $filename . '.ssml'))
            ->with('message', 'Conversion Successful!');
    }
}
