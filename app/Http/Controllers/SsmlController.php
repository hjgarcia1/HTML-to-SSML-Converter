<?php

namespace App\Http\Controllers;

use App\Ssml;
use App\SSMLTransformer;
use Storage;

/**
 * Class SsmlController
 * @package App\Http\Controllers
 */
class SsmlController extends Controller
{
    /**
     * Show all SSMLs
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (request()->has('query')) {
            $ssmls = Ssml::where('title', 'LIKE', '%' . request('query') . '%')->paginate(25);
        } else {
            $ssmls = Ssml::paginate(25);
        }

        return view('ssml.index', compact('ssmls'));
    }

    /**
     * Show the create form
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        return view('ssml.create');
    }

    /**
     * Store an SSML
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store()
    {
        $data = $this->validateData();

        //set the file name
        $filename = Ssml::getFilename($data['title']);
        $ssml = Ssml::generate($data['html'], $filename);

        Ssml::create([
            'title' => $data['title'],
            'link' => Ssml::getFilePath($filename),
            'html' => $data['html'],
            'content' => $ssml->content,
        ]);

        //redirect back to the home page with message
        return redirect('/')
            ->with('link', 'Use this link to get the file: ' . Ssml::getFilePath($filename))
            ->with('message', 'Conversion Successful!');
    }

    /**
     * Edit SSML
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $ssml = Ssml::find($id);

        return view('ssml.edit', compact('ssml'));
    }

    /**
     * Update an SSML
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id)
    {
        $ssml = Ssml::find($id);

        Storage::disk('public_uploads')->delete(basename($ssml->link));

        $filename = Ssml::getFilename(request('title'));
        $newSsml = Ssml::generate(request('html'), $filename);
        $ssml->update([
            'title' => request('title'),
            'link' => Ssml::getFilePath($filename),
            'html' => request('html'),
            'content' => $newSsml->content,
        ]);

        return redirect('/ssml/' . $id)->with('message', 'SSML was updated!');
    }

    /**
     * Delete SSML
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function delete($id)
    {
        $ssml = Ssml::find($id);

        $ssml->delete();

        Storage::disk('public_uploads')->delete(basename($ssml->link));

        return redirect('/')
            ->with('message', 'SSML file was deleted!');
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
}
