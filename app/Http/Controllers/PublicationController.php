<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication;
use App\Models\Article;
use App\Models\ClientReport;
use App\Models\Monograph;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PublicationController extends Controller
{
    public function index()
    {
        return response()->json(Publication::all());
    }

    public function store(Request $request)
    {
//        dd($request->input());
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'summary' => 'required',
            'type' => 'required',
            'file_path' => 'required|mimes:pdf',
            'authors' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $publication = new Publication();
        $publication->title = $request->input('title');
        $publication->summary = $request->input('summary');
        $publication->type = $request->input('type');

        try {
            $fileName = Str::random() . '.' . $request->file_path->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('publications', $request->file_path, $fileName);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something goes wrong while creating a product!!'
            ], 500);
        }

        try {
            $file = $request->file('file_path');
            $fileName = Str::random() . '.' . $request->file_path->getClientOriginalExtension();
            $file->move(public_path('publications'), $fileName);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something goes wrong while creating a product!!'
            ], 500);
        }

        $publication->file_path = $fileName;
        $publication->save();

        $publication->authors()->sync($request->input('authors'));

        switch ($request->input('type')) {
            case 'articles':
                $validator = Validator::make($request->all(), [
                    'magazine' => 'required',
                    'start_page' => 'required',
                    'end_page' => 'required',
                ]);

                if ($validator->fails()) {
                    break;
                }
                $article = new Article();
                $article->publication_id = $publication->id;
                $article->magazine = $request->input('magazine');
                $article->start_page = $request->input('start_page');
                $article->end_page = $request->input('end_page');
                $article->save();
                break;

            case 'client_reports':
                $validator = Validator::make($request->all(), [
                    'client_name' => 'required',
                    'project_name' => 'required',
                ]);
                if ($validator->fails()) {
                    break;
                }
                $clientReport = new ClientReport();
                $clientReport->publication_id = $publication->id;
                $clientReport->client_name = $request->input('client_name');
                $clientReport->project_name = $request->input('project_name');
                $clientReport->save();
                break;

            case 'monographs':
                $validator = Validator::make($request->all(), [
                    'subject' => 'required',
                    'number_of_pages' => 'required',
                ]);
                if ($validator->fails()) {
                    break;
                }
                $monograph = new Monograph();
                $monograph->publication_id = $publication->id;
                $monograph->subject = $request->input('subject');
                $monograph->number_of_pages = $request->input('number_of_pages');
                $monograph->save();
                break;
        }

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'publication' => $publication,
        ]);
    }
}
