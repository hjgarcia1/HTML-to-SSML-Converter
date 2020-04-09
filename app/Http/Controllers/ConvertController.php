<?php

namespace App\Http\Controllers;

use App\Ssml;
use App\SSMLTransformer;
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
        $filename = \Str::slug($data['name']) . '.ssml';

        $ssmlTransformer = new SSMLTransformer($data['html']);

        $ssmlTransformer->save($filename);

        Ssml::create([
            'title' => $data['name'],
            'link' => url('storage/' . $filename),
            'content' => $ssmlTransformer->html,
        ]);

        //redirect back to the home page with message
        return redirect('/')
            ->with('link', 'Use this link to get the file: ' . url('storage/' . $filename))
            ->with('message', 'Conversion Successful!');
    }
}
