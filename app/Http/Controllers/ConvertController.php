<?php

namespace App\Http\Controllers;

use App\Ssml;
use App\SSMLTransformer;
use Storage;

class ConvertController extends Controller
{
    public function show()
    {
        return view('ssml.create');
    }

    public function transform()
    {
        $data = $this->validateData();

        //set the file name
        $filename = $this->generateFilename($data['title']);
        $ssml = $this->generateSsml($data['html'], $filename);

        Ssml::create([
            'title' => $data['title'],
            'link' => $this->getFilePath($filename),
            'html' => $data['html'],
            'content' => $ssml->content,
        ]);

        //redirect back to the home page with message
        return redirect('/')
            ->with('link', 'Use this link to get the file: ' . $this->getFilePath($filename))
            ->with('message', 'Conversion Successful!');
    }

    public function edit($id)
    {
        $ssml = Ssml::find($id);
        return view('ssml.edit',compact('ssml'));
    }

    /**
     * @param $html
     * @param string $filename
     * @return SSMLTransformer
     */
    protected function generateSsml($html, string $filename): SSMLTransformer
    {
        $ssml = new SSMLTransformer($html);

        $ssml
            ->removeTag('br')
            ->removeTag('img')
            ->appendTo('<break />', 'p')
            ->appendTo('<break />', 'h2')
            ->appendAttr('break', ['time' => '800ms'])
            ->save($filename);

        return $ssml;
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
     * @return array
     */
    protected function validateData(): array
    {
        return $data = request()->validate([
            'title' => 'required',
            'html' => 'required|max:5000',
        ]);
    }

    /**
     * @param $name
     * @return string
     */
    protected function generateFilename($name): string
    {
        return \Str::slug($name) . '.ssml';
    }
}
