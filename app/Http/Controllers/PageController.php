<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;




class PageController extends Controller
{
  /**
   * Display a listing of pages.
   */
  public function index(Request $request)
  {
    // For regular page load with server-side pagination
    $search = $request->input('search');
    $perPage = $request->input('per_page', 10);
    $sort = $request->input('sort', 'id');
    $order = $request->input('order', 'desc');
    $type = $request->input('type');

    $query = Page::query();

    // Apply search filter if provided
    if ($search) {
      $query->where(function ($q) use ($search) {
        // Search in JSON fields (title)
        $q->whereRaw("JSON_CONTAINS(title, '\"" . $search . "\"', '$.en')")
          ->orWhereRaw("JSON_CONTAINS(title, '\"" . $search . "\"', '$.ar')");
      });
    }

    // Filter by type if provided
    if ($type) {
      $query->where('type', $type);
    }

    // Apply sorting
    $query->orderBy($sort, $order);

    // Get paginated results
    $pages = $query->paginate($perPage);

    // Get statistics data
    $totalPages = Page::count();
    $activePages = Page::where('status', true)->count();
    $inactivePages = Page::where('status', false)->count();
    $pageTypes = Page::select('type')->distinct()->count('type');

    return view('content.shared.pages.index', [
      'pages' => $pages,
      'totalPages' => $totalPages,
      'activePages' => $activePages,
      'inactivePages' => $inactivePages,
      'pageTypes' => $pageTypes,
      'search' => $search,
      'sort' => $sort,
      'order' => $order,
      'perPage' => $perPage,
      'type' => $type
    ]);
  }

  /**
   * Show the form for creating a new page.
   */
  public function create()
  {
    return view('admin.pages.create');
  }

  /**
   * Store a newly created page in storage.
   */
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'title_en' => 'required|string|max:255',
      'title_ar' => 'required|string|max:255',
      'description_en' => 'required|string',
      'description_ar' => 'required|string',
      'type' => 'required|string|max:50',
      'seo_title' => 'nullable|string|max:255',
      'seo_description' => 'nullable|string|max:255',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    $page = new Page();
    $page->setTranslation('title', 'en', $request->title_en);
    $page->setTranslation('title', 'ar', $request->title_ar);
    $page->setTranslation('description', 'en', $request->description_en);
    $page->setTranslation('description', 'ar', $request->description_ar);
    $page->type = $request->type;
    $page->status = $request->has('status');
    $page->seo_title = $request->seo_title;
    $page->seo_description = $request->seo_description;
    $page->save();

    if ($request->ajax()) {
      return response()->json($page, 201);
    }

    return redirect()->route('admin.pages.index')
      ->with('success', 'Page created successfully.');
  }

  /**
   * Display the specified page.
   */
  public function show(Page $page)
  {
    return view('admin.pages.show', compact('page'));
  }

  /**
   * Show the form for editing the specified page.
   */
  public function edit(Page $page)
  {
    if (request()->ajax()) {
      return response()->json([
        'id' => $page->id,
        'title_en' => $page->getTranslation('title', 'en'),
        'title_ar' => $page->getTranslation('title', 'ar'),
        'description_en' => $page->getTranslation('description', 'en'),
        'description_ar' => $page->getTranslation('description', 'ar'),
        'type' => $page->type,
        'status' => $page->status,
        'seo_title' => $page->seo_title,
        'seo_description' => $page->seo_description,
      ]);
    }

    return view('admin.pages.edit', compact('page'));
  }

  /**
   * Update the specified page in storage.
   */
  public function update(Request $request, Page $page)
  {
    $validator = Validator::make($request->all(), [
      'title_en' => 'required|string|max:255',
      'title_ar' => 'required|string|max:255',
      'description_en' => 'required|string',
      'description_ar' => 'required|string',
      'type' => 'required|string|max:50',
      'seo_title' => 'nullable|string|max:255',
      'seo_description' => 'nullable|string|max:255',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 422);
    }

    $page->setTranslation('title', 'en', $request->title_en);
    $page->setTranslation('title', 'ar', $request->title_ar);
    $page->setTranslation('description', 'en', $request->description_en);
    $page->setTranslation('description', 'ar', $request->description_ar);
    $page->type = $request->type;
    $page->status = $request->has('status');
    $page->seo_title = $request->seo_title;
    $page->seo_description = $request->seo_description;
    $page->save();

    if ($request->ajax()) {
      return response()->json($page, 200);
    }

    return redirect()->route('admin.pages.index')
      ->with('success', 'Page updated successfully.');
  }

  /**
   * Remove the specified page from storage.
   */
  public function destroy(Page $page)
  {
    try {
      $page->delete();

      if (request()->ajax()) {
        return response()->json(['success' => true]);
      }

      return redirect()->route('admin.pages.index')
        ->with('success', 'Page deleted successfully.');
    } catch (\Exception $e) {
      if (request()->ajax()) {
        return response()->json(['error' => $e->getMessage()], 500);
      }

      return redirect()->route('admin.pages.index')
        ->with('warning', 'Error deleting page: ' . $e->getMessage());
    }
  }
}
