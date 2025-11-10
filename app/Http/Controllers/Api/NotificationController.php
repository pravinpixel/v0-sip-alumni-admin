<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $sortColumn = $request->get('sort_column', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $data = Notification::where('to_id', Auth::user()->id)
                ->with(['assignby','mentionedTask','mentionedInvoice'])
                ->orderBy($sortColumn, $sortOrder)->get();

            $total_count = $data->where('seen', 0)->count();
        } catch (\Throwable $e) {
            return $this->returnError($e->getMessage());
        }
        return response()->json([
            'success' => true,
            'total_count' => $total_count,
            'data' => $data
        ]);
    }

    public function delete(Request $request, $id)
    {
        try {
            $data = Notification::find($id);
            if (!$data) {
                return $this->returnError('Notification not found');
            }
            $data->delete();

            return $this->returnSuccess('', 'Notification removed successfully');
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => $e->getMessage()], 422);
        }
    }
    public function seenUpdate($id)
    {
        try {
            $data = Notification::find($id);
            $data->seen = 1;
            $data->update();
            return $this->returnSuccess($data, 'Notification seen successfully');
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => $e->getMessage()], 422);
        }
    }
    public function alldelete(Request $request)
    {
        try {
            $datas = Notification::where('to_id', Auth::user()->id)->get();
            foreach ($datas as $data) {
                $data->delete();
            }
            return $this->returnSuccess('', ' All notification removed  successfully');
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'errors' => $e->getMessage()], 422);
        }
    }
}
