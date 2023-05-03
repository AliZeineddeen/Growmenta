<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\TrainingSection;
use Illuminate\Http\Request;

class TrainingSectionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Form validation
        $request->validate([
            'section_title' => 'required',
            'title' => 'required',
        ]);

        // Get All Request
        $input = $request->all();

        // Record to database
        TrainingSection::firstOrCreate([
            'language_id' => getLanguage()->id,
            'section_title' => $input['section_title'],
            'title' => $input['title']
        ]);

        return redirect()->route('training.create')
            ->with('success', 'content.created_successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Form validation
        $request->validate([
            'section_title' => 'required',
            'title' => 'required',
        ]);

        // Get All Request
        $input = $request->all();

        // Update model
        TrainingSection::find($id)->update($input);

        return redirect()->route('training.create')
            ->with('success', 'content.updated_successfully');
    }
}
