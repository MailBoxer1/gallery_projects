<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProjectResource::collection(Project::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $request->validated();
        $image = $request->file('image');
        $path = $image->store('public/images');

        $projectData = [
            'title' => $request->get('title'),
            'description' => $request->get('description'),
            'image' => $path
        ];

        $project = Project::create($projectData);

        return ProjectResource::make($project);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return ProjectResource::make($project);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $request->validated();

        $id = $project->getKey();

        $image = $request->file('image');
        if (isset($image)) {
            $model = Project::find($id);
            $imagePath = $model->image;
            Storage::delete($imagePath);

            $path = $image->store('public/images');
        }
        if (isset($path)) {
            $projectData = [
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'image' => $path
            ];
        } else {
            $projectData = [
                'title' => $request->get('title'),
                'description' => $request->get('description'),
            ];
        }


        $project->update($projectData);

        return ProjectResource::make($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response()->noContent();
    }
}
