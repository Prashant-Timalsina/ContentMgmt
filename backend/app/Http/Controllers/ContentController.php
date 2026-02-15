<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    
    // List all articles (role-based visibility)
    public function index(Request $request)
    {
        $this->authorize('viewAny',Content::class);
        //
        $user = $request->user();

        // For admins, all articles are available in all states(i wanted draft to be excluded.)
        if($user->can('publish_articles')){
            return Content::with('type','author')->latest()->get();
        }

        // For editors, editor's own articles
        return Content::where('author_id',$user->id)
            ->with('type')->latest()->get();
    }

    //Every User can see the published articles
    public function showAll(){
        return Content::where('status','published')
            ->with(['author','type'])
            ->latest('published_at')
            ->get();
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
     * Display the specified resource.
     */
    public function show(Request $request, Content $content)
    {
        // Allow public access to published articles, require auth for others
        if ($content->status !== Content::STATUS_PUBLISHED) {
            // For non-published articles, user must be authenticated
            if (!$request->user()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $this->authorize('view', $content);
        }

        return $content->load('type','author');
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
