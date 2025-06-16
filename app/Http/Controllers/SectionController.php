<?php

namespace App\Http\Controllers;

use App\Http\Requests\Section\StoreSectionRequest;
use App\Http\Requests\Section\UpdateSectionRequest;
use App\Http\Resources\Section\SectionResource;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SectionController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search', null);
        $locale = app()->getLocale();
        $jsonPath = '$.' . $locale;

        $sections = Section::query()
            ->when($search, function ($query) use ($search, $jsonPath) {
                $query->where(function ($q) use ($jsonPath, $search) {
                    $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, ?)) LIKE ?", [$jsonPath, "%{$search}%"])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, ?)) LIKE ?", [$jsonPath, "%{$search}%"]);
                });
            })
            ->paginate(5);

        if ($sections->isEmpty()) {
            return success([
                'pagination' => [
                    'current_page' => $sections->currentPage(),
                    'last_page' => $sections->lastPage(),
                    'per_page' => $sections->perPage(),
                    'total' => $sections->total(),
                ]
            ], 200, 'No sections found for the given criteria.');
        }

        if ($sections->currentPage() > $sections->lastPage()) {
            return redirect()->route('sections.index', ['page' => 1]);
        }

        return SectionResource::collection($sections);
    }



    public function store(StoreSectionRequest $request)
    {
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('sections', 'public');
        } else {
            $imagePath = null;
        }

        $section = Section::create([
            'name' => ['ar' => $request->ar_name, 'en' => $request->en_name],
            'description' => ['ar' => $request->ar_description, 'en' => $request->en_description],
            'image' => $imagePath
        ]);

        $locale = app()->getLocale();
        $newSection = [
            'name' => $section->getName($locale),
            'description' => $section->getDescription($locale),
            'image' => $imagePath
        ];

        return success(['section' => $newSection], 201);
    }



    public function show(Section $section)
    {
        return success(['section' => $section]);
    }



    public function update(UpdateSectionRequest $request, Section $section)
    {
        $data = $request->safe()->only(['ar_name', 'en_name', 'ar_description', 'en_description']);

        if ($request->hasFile('image')) {
            if ($section->image && Storage::disk('public')->exists($section->image)) {
                Storage::disk('public')->delete($section->image);
            }
            $section->image = $request->file('image')->store('sections', 'public');
        }

        if ($request->filled(['ar_name', 'en_name'])) {
            $existing = array_merge(['ar' => null, 'en' => null], $section->name);
            $override = array_filter([
                'ar' => $data['ar_name'] ?? null,
                'en' => $data['en_name'] ?? null,
            ]);
            $section->name = array_merge($existing, $override);
        }

        if ($request->filled(['ar_description', 'en_description'])) {
            $existing = array_merge(['ar' => null, 'en' => null], $section->description);
            $override = array_filter([
                'ar' => $data['ar_description'] ?? null,
                'en' => $data['en_description'] ?? null,
            ]);
            $section->description = array_merge($existing, $override);
        }

        if (!$section->isDirty() && !$request->hasFile('image')) {
            return success([
                'section' => $section,
            ], 200, 'Nothing updated.');
        }

        $section->save();
        return success(['section' => $section->fresh()], 200);
    }


    public function destroy(Section $section)
    {
        if (Storage::disk('public')->exists($section->image)) {
            Storage::disk('public')->delete($section->image);
        }
        $section->delete();
        return success(['message' => 'Section deleted successfully'], 200);
    }
}
