<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Policies\AdminContentPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    // Public User sees all published articles
    public function indexPublic(){
        return Content::where('status',Content::STATUS_PUBLISHED)
            ->with('type','author')
            ->latest('published_at')
            ->get();
    }

    //Public Single article
    public function show(Content $content)
    {
        $this->authorize('view',$content);

        return $content->load('author','type');
    }

    // Author specific articles.
    public function myArticles(Request $request){
        $this->authorize('viewOwn',Content::class);

        return Content::where('author_id', $request->user()->id)
            ->with('type')
            ->latest()
            ->get();
    }
    

    // List all articles (role-based visibility)
    public function allArticles(){
        $this->authorize('viewAll',Content::class);

        $contents = Content::where('status', '!=', Content::STATUS_DRAFT)
            ->with('author','type')
            ->latest('created_at')
            ->get();
        return $contents;
    }


    // A draft article
    public function store(Request $request)
    {
        $this->authorize('create', Content::class);

        $validated = $request->validate([
            'title'=> 'required|string',
            'body' => 'required|string',
            'type_id'=>'required|exists:article_types,id',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        $validated['author_id'] = $request->user()->id;
        $validated['status'] = Content::STATUS_DRAFT;

        $article = Content::create($validated);

        return response()->json($article,201);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Content $content)
    {
        $this->authorize('update', $content);


        $validated = $request->validate([
            'title' => 'sometimes|string',
            'body' => 'sometimes|string',
            'type_id' => 'sometimes|exists:article_types,id'
        ]);

        if(isset($validated['title'])){
            $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        }

        if(($validated['status'] ?? null)===Content::STATUS_PUBLISHED){
            $validated['published_at'] = now();
        }

        $content->update($validated);

        return response()->json($content);
    }


    // Delete the articles
    public function destroy(Content $content)
    {
        $this->authorize('delete', $content);

        $content->delete();

        return response()->json(['message' => 'Deleted Successfully']);
    }


    public function submit(Content $content)
    {
         $this->authorize('submit', $content);

        $content->update([
            'status'=>Content::STATUS_SUBMITTED,
            'submitted_at'=>now()
        ]);

        return response()->json(['message'=>'Submitted for review']);
    }


    public function publish(Content $content){
        $this->authorize('publish', $content);

        $content->update([
            'status'=>Content::STATUS_PUBLISHED,
            'published_at'=>now()
        ]);
        return response()->json([
            'message'=>'Published successfully'
        ]);
    }

    public function reject(Request $request, Content $content)
    {
        $this->authorize('reject', $content);

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $content->update([
            'status' => Content::STATUS_REJECTED,
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        return response()->json(['message' => 'Article rejected']);
    }
}
