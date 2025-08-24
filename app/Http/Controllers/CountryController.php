<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Country;


class CountryController extends Controller
{
    /**
     * Display a listing of countries.
     */
    public function index(Request $request)
    {
        // For DataTables AJAX request
        if ($request->ajax()) {
            $columns = [
                1 => 'id',
                2 => 'name_ar',
                3 => 'name_en',
                4 => 'code',
                5 => 'exchange_rate',
                6 => 'country_tax',
                7 => 'status',
            ];
            $search = $request->input('search.value');
            $query = Country::query()
                ->when($search, function ($query, $search) {
                    return $query->where('id', 'LIKE', "%{$search}%")
                        ->orWhere('name_ar', 'LIKE', "%{$search}%")
                        ->orWhere('name_en', 'LIKE', "%{$search}%")
                        ->orWhere('code', 'LIKE', "%{$search}%");
                });
            $totalData = $query->count();
            $totalFiltered = $totalData;
            $countries = $query->orderBy($columns[$request->input('order.0.column')], $request->input('order.0.dir'))
                ->offset($request->input('start'))
                ->limit($request->input('length'))
                ->get();
            $data = $countries->map(function ($country, $index) use ($request) {
                return [
                    'id' => $country->id,
                    'fake_id' => $request->input('start') + $index + 1,
                    'name_ar' => $country->name_ar,
                    'name_en' => $country->name_en,
                    'code' => $country->code,
                    'exchange_rate' => $country->exchange_rate,
                    'country_tax' => $country->country_tax,
                    'status' => $country->status,
                ];
            });
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => intval($totalData),
                'recordsFiltered' => intval($totalFiltered),
                'data' => $data,
            ]);
        }

        // For regular page load with server-side pagination
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'desc');

        $query = Country::query();

        // Apply search filter if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'LIKE', "%{$search}%")
                    ->orWhere('name_en', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%")
                    ->orWhere('id', 'LIKE', "%{$search}%");
            });
        }

        // Apply sorting
        $query->orderBy($sort, $order);

        // Get paginated results
        $countries = $query->paginate($perPage);

        // Get statistics data
        $totalCountries = Country::count();
        $activeCountries = Country::where('status', true)->count();
        $inactiveCountries = $totalCountries - $activeCountries;

        return view('content.shared.countries.index', [
            'countries' => $countries,
            'totalCountries' => $totalCountries,
            'activeCountries' => $activeCountries,
            'inactiveCountries' => $inactiveCountries,
            'search' => $search,
            'sort' => $sort,
            'order' => $order,
            'perPage' => $perPage
        ]);
    }

    /**
     * Show the form for editing the specified country.
     */
    public function edit(Country $country)
    {
        if (request()->ajax()) {
            return response()->json($country);
        }

        return redirect()->route('admin.countries.index');
    }

    /**
     * Store a newly created country.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'code' => 'required|string|unique:countries,code,' . $request->id,
            'exchange_rate' => 'required|numeric|min:0',
            'country_tax' => 'nullable|numeric|min:0|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'symbol_ar' => 'nullable|string|max:255',
            'symbol_en' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'code' => $request->code,
            'exchange_rate' => $request->exchange_rate,
            'country_tax' => $request->country_tax,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'symbol_ar' => $request->symbol_ar,
            'symbol_en' => $request->symbol_en,
            'status' => $request->has('status') ? 1 : 0,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($request->id) {
                $country = Country::find($request->id);
                if ($country && $country->image) {
                    Storage::disk('public')->delete($country->image);
                }
            }
            $image = $request->file('image');
            $data['image'] = $image->store('countries', 'public');
        }

        $country = Country::updateOrCreate(['id' => $request->id], $data);

        if ($request->ajax()) {
            return response()->json($country, $request->id ? 200 : 201);
        }

        return redirect()->route('admin.countries.index')
            ->with('success', 'Country ' . ($request->id ? 'updated' : 'created') . ' successfully.');
    }

    /**
     * Update the specified country.
     */
    public function update(Request $request, Country $country)
    {
        // Redirect to store method as it handles both create and update
        return $this->store($request);
    }

    /**
     * Remove the specified country.
     */
    public function destroy(Country $country)
    {
        try {
            // Check if country has related users
            if ($country->users()->count() > 0) {
                throw new \Exception('Cannot delete country with associated users.');
            }

            // Check if country has related cities
            if ($country->cities()->count() > 0) {
                throw new \Exception('Cannot delete country with associated cities.');
            }

            // Delete country image if exists
            if ($country->image) {
                Storage::disk('public')->delete($country->image);
            }

            $country->delete();

            if (request()->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('admin.countries.index')
                ->with('success', 'Country deleted successfully.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return redirect()->route('admin.countries.index')
                ->with('warning', 'Error deleting country: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified country.
     */
    public function show(Country $country)
    {
        // Get related data
        $cities = $country->cities()->paginate(10);
        $userCount = $country->users()->count();

        return view('content.shared.countries.show', [
            'country' => $country,
            'cities' => $cities,
            'userCount' => $userCount
        ]);
    }
}
