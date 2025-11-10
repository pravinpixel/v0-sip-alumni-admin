<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Validation\Rule;


class LocationController extends Controller
{
    public function index(Request $request)
    {

        $search = $request->input('search');
        $status = $request->input('status');
        $perPage = $request->input('pageItems');

        $query = Location::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        if ($status === '1' || $status === '0') {
            $query->where('status', $status);
        }

        $datas = $query->orderBy('id', 'desc')->paginate($perPage);
        $currentPage = $datas->currentPage();
        $serialNumberStart = ($currentPage - 1) * $perPage + 1;

        $total_count = Location::count();


        return view('masters.location.index', [

            'datas' => $datas,
            'selectedStatus' => $status,
            'search' => $search,
            'total_count' => $total_count,
            'serialNumberStart' => $serialNumberStart,
        ]);
    }

    public function get(Request $request, $id)
    {
        $data = Location::find($id);

        return response()->json($data);
    }

    public function save(Request $request)
    {
        $form_data = $request->validate([
            'name' => [
                'required', 'string', 'max:200',
                Rule::unique('locations')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })
            ],
            'status' => 'required|boolean',

        ]);
        try {

            $location = new Location;
            $location->name = $request->input('name');
            $location->status = $request->input('status');
            $location->save();

            return response()->json(['message' => 'Location created successfully', 'data' => $location]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => $e->getMessage()], 422);
        }
    }


    public function update(Request $request, $id)
    {
        $form_data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:200',
                Rule::unique('locations')->whereNull('deleted_at')->ignore($id),
            ],
            'status' => 'required|boolean',

        ]);
        try {

            $location = Location::find($id);
            $location->name = $request->input('name');
            $location->status = $request->input('status');
            $location->save();
            return response()->json(['message' => 'Location updated successfully', 'data' => $location]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => $e->getMessage()], 422);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = Location::find($id)->delete();
            return response()->json(['message' => 'Location deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => $e->getMessage()], 422);
        }
    }
}
