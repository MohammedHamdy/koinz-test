<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\BookUserRequest;
use App\Models\BookUser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookUserController extends Controller
{
    use Helper;

    public function index(): JsonResponse
    {
        try {
            $items = BookUser::with(['book','user'])->paginate(10);
            if ($items->isEmpty()) {
                return $this->outApiJson('not-found-data', trans('main.not_found_data'));
            }
            return $this->outApiJson('success', trans('main.success'), $items);
        } catch (Exception $th) {
            return $this->outApiJson('exception', $this->getExceptionMessage($th));
        }
    }

    public function store(BookUserRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $item = BookUser::create($request->validated());
            $item->save();
            if (!$item) {
                DB::rollback();
                return $this->outApiJson('error-insert', trans('main.error_insert'));
            }
            DB::commit();
            return $this->outApiJson('success', trans('main.created_successfully'), $item);
        } catch (Exception $th) {
            return $this->outApiJson('exception', $this->getExceptionMessage($th));
        }
    }  
}
