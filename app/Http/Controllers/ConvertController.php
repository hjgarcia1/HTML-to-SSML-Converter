<?php

namespace App\Http\Controllers;

use App\Ssml;
use Storage;

class ConvertController extends Controller
{
    public function show()
    {
        return view('converter');
    }

    public function transform()
    {
        $data = request()->validate([
            'name' => 'required',
            'html' => 'required|max:5000',
        ]);

        //set the file name
        $filename = \Str::slug($data['name']);

        //create the file
        Storage::disk('public_uploads')->put($filename . '.ssml', $data['html']);

        Ssml::create([
            'title' => $data['name'],
            'link' => url('storage/' . $filename . '.ssml'),
            'content' => $data['html'],
        ]);

        //redirect back to the home page with message
        return redirect('/')
            ->with('link', 'Use this link to get the file: ' . url('storage/' . $filename . '.ssml'))
            ->with('message', 'Conversion Successful!');
    }
}
