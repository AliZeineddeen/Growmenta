<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Training;
use App\Models\Admin\TrainingSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TrainingController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Retrieving a model
        $language = getLanguage();
        $trainings = Training::where('language_id', $language->id)->orderBy('id', 'desc')->get();
        $training_section = TrainingSection::where('language_id', $language->id)->first();

        return view('admin.training.create', compact('trainings', 'training_section'));
    }

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
            'type' => 'in:icon,image',
            'title' => 'required',
            'order' => 'required|integer',
            'training_image' => 'mimes:svg,png,jpeg,jpg|max:2048',
        ]);

        // Get All Request
        $input = $request->all();

        if($request->hasFile('training_image')){

            // Get image file
            $training_image = $request->file('training_image');

            // Folder path
            $folder ='uploads/img/trainings/';

            // Make image name
            $training_image_name =  time().'-'.$training_image->getClientOriginalName();

            // Upload image
            $training_image->move($folder, $training_image_name);

            // Set input
            $input['training_image']= $training_image_name;

        } else {
            // Set input
            $input['training_image']= null;
        }

        // Record to database
        Training::create([
            'language_id' => getLanguage()->id,
            'type' => $input['type'],
            'icon' => $input['icon'],
            'training_image' => $input['training_image'],
            'title' => $input['title'],
            'desc' => $input['desc'],
            'order' => $input['order']
        ]);

        return redirect()->route('training.create')
            ->with('success', 'content.created_successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Retrieving models
        $training = Training::findOrFail($id);

        return view('admin.training.edit', compact('training'));
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
            'type' => 'in:icon,image',
            'title' => 'required',
            'order' => 'required|integer',
            'training_image' => 'mimes:svg,png,jpeg,jpg|max:2048',
        ]);

        // Get model
        $training = Training::find($id);

        // Get All Request
        $input = $request->all();

        if($request->hasFile('training_image')){

            // Get image file
            $training_image = $request->file('training_image');

            // Folder path
            $folder ='uploads/img/trainings/';

            // Make image name
            $training_image_name =  time().'-'.$training_image->getClientOriginalName();

            // Delete Image
            File::delete(public_path($folder.$training->training_image));

            // Upload image
            $training_image->move($folder, $training_image_name);

            // Set input
            $input['training_image'] = $training_image_name;

        }

        // Record to database
        Training::find($id)->update($input);

        return redirect()->route('training.create')
            ->with('success', 'content.updated_successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Retrieve a model
        $training = Training::find($id);

        // Folder path
        $folder = 'uploads/img/trainings/';

        // Delete Image
        File::delete(public_path($folder.$training->training_image));

        // Delete record
        $training->delete();

        return redirect()->route('training.create')
            ->with('success', 'content.deleted_successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_checked(Request $request)
    {
        // Get All Request
        $input = $request->input('checked_lists');

        $arr_checked_lists = explode(",", $input);

        if (array_filter($arr_checked_lists) == []) {
            return redirect()->route('training.create')
                ->with('warning', 'content.please_choose');
        }

        foreach ($arr_checked_lists as $id) {

            // Retrieve a model
            $training = Training::findOrFail($id);

            // Folder path
            $folder = 'uploads/img/trainings/';

            // Delete Image
            File::delete(public_path($folder.$training->training_image));

            // Delete record
            $training->delete();

        }

        return redirect()->route('training.create')
            ->with('success', 'content.deleted_successfully');
    }
}
