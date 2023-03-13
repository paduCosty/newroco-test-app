<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication;
use App\Models\Article;
use App\Models\ClientReport;
use App\Models\Monograph;
use Illuminate\Support\Facades\Validator;

class PublicationController extends Controller
{
    public function store(Request $request)
    {
//        dd($request->all());
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'summary' => 'required',
            'type' => 'required',
            'file' => 'required|mimes:pdf',
            'authors' => 'required|array|min:1',
            'authors.*' => 'required|exists:authors,id',
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
        $publication->file_path = $request->file('file')->store('publications');
        $publication->save();

        $publication->authors()->sync($request->input('authors'));

        switch ($request->input('type')) {
            case 'article':
                $article = new Article();
                $article->publication_id = $publication->id;
                $article->magazine = $request->input('magazine');
                $article->start_page = $request->input('start_page');
                $article->end_page = $request->input('end_page');
                $article->save();
                break;
            case 'client_report':
                $clientReport = new ClientReport();
                $clientReport->publication_id = $publication->id;
                $clientReport->client_name = $request->input('client_name');
                $clientReport->project_name = $request->input('project_name');
                $clientReport->save();
                break;
            case 'monograph':
                $monograph = new Monograph();
                $monograph->publication_id = $publication->id;
                $monograph->subject = $request->input('subject');
                $monograph->number_of_pages = $request->input('number_of_pages');
                $monograph->save();
                break;
        }

        return response()->json([
            'status' => 'success',
            'publication' => $publication,
        ]);
    }
}
