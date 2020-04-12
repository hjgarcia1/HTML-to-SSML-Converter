<?php

namespace App\Http\Controllers;

use App\Http\Requests\SsmlRequest;
use App\Ssml;
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
     * @param SsmlRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(SsmlRequest $request)
    {
        $filename = Ssml::getFilename($request->get('title'));

        $ssml = Ssml::generate($request->get('html'), $filename);

        Ssml::create([
            'title' => $request->get('title'),
            'link' => Ssml::getFilePath($filename),
            'html' => $request->get('html'),
            'content' => $ssml->content,
        ]);

        //create mp3
        exec("java -jar " . app_path('Converter/google-tts.jar') . " " . public_path('storage/' . $filename) . " " . public_path('readings/' . $filename . '.mp3') . " 0.87");

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
     * @param SsmlRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(SsmlRequest $request, $id)
    {
        $ssml = Ssml::find($id);

        Storage::disk('public_uploads')->delete(basename($ssml->link));

        $filename = Ssml::getFilename(request('title'));
        $newSsml = Ssml::generate(request('html'), $filename);
        $ssml->update([
            'title' => $request->get('title'),
            'link' => Ssml::getFilePath($filename),
            'html' => $request->get('html'),
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
}
