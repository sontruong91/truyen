<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\Helper;
use App\Repositories\Chapter\ChapterRepositoryInterface;
use App\Repositories\Story\StoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChapterController extends Controller
{
    public function __construct(
        protected StoryRepositoryInterface $storyRepository,
        protected ChapterRepositoryInterface $chapterRepository
    )
    {
        
    }

    public function index(Request $request, $slugStory, $slugChapter) {
        $story = $this->storyRepository->getStoryBySlug($slugStory);
        if (!$story) {
            return abort(404);
        }

        $chapter = $this->chapterRepository->getChapterSingle($story->id, $slugChapter);

        $setting = Helper::getSetting();
        $objectSEO = (object) [
            'name' => $chapter->name,
            'description' => Str::limit($story->desc, 30),
            'keywords' => 'doc truyen, doc truyen online, truyen hay, truyen chu',
            'no_index' => $setting ? !$setting->index : env('NO_INDEX'),
            'meta_type' => 'Book',
            'url_canonical' => url()->current(),
            'image' => asset($story->image),
            'site_name' => $chapter->name,
        ];

        $objectSEO->article   = [
            'author'         => $story->author->name,
            'published_time' => $story->created_at->toAtomString(),
        ];

        Helper::setSEO($objectSEO);
        
        return view('Frontend.chapter', compact('story', 'chapter', 'slugChapter'));
    }

    public function getChapters(Request $request) {
        $res = ['success' => false];

        $listChapter = $this->chapterRepository->getListChapterByStoryId($request->input('story_id'));
    
        $res['chapters'] = $listChapter;
        $res['success'] = true;

        return response()->json($res);
    }
}
