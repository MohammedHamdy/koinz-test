<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\BookUserRequest;
use App\Models\BookUser;
use App\Models\User;
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
            
            $this->sendThanksMessage($request->user_id);
            
            return $this->outApiJson('success', trans('main.created_successfully'), $item);
        } catch (Exception $th) {
            return $this->outApiJson('exception', $this->getExceptionMessage($th));
        }
    }

    public function mostReadBooks(): JsonResponse
    {
        try {
            $topReadBooks = \DB::select("
            WITH RECURSIVE t AS (
                SELECT 
                    book_id,
                    start_page,
                    MAX(end_page) AS end_page,
                    ROW_NUMBER() OVER (PARTITION BY book_id ORDER BY start_page) AS rn
                FROM 
                    book_user
                GROUP BY 
                    book_id, start_page
            ),
            r AS (
                SELECT 
                    0 AS lvl,
                    bu.book_id,
                    bu.start_page,
                    bu.end_page,
                    bu.rn
                FROM 
                    t AS bu
                WHERE 
                    NOT EXISTS (
                        SELECT 
                            1
                        FROM 
                            t AS bu2
                        WHERE 
                            bu2.book_id = bu.book_id
                            AND bu2.rn < bu.rn
                            AND bu.start_page BETWEEN bu2.start_page AND bu2.end_page
                    )
                UNION ALL
                SELECT 
                    lvl + 1,
                    r.book_id,
                    r.start_page,
                    t.end_page,
                    t.rn
                FROM 
                    r
                INNER JOIN 
                    t ON t.book_id = r.book_id
                    AND t.rn > r.rn
                    AND r.end_page BETWEEN t.start_page AND r.end_page
            )
            SELECT 
                book_id,
                book_name,
                SUM(end_page - start_page + 1) AS num_of_read_pages
            FROM (
                SELECT 
                    book_id,
                    start_page,
                    MAX(end_page) AS end_page
                FROM 
                    r
                GROUP BY 
                    book_id, start_page
            ) AS gr
            JOIN 
                books ON books.id = gr.book_id
            GROUP BY 
                book_id
            ORDER BY 
                num_of_read_pages DESC
            LIMIT 5;
        ");
        
            return $this->outApiJson('success', trans('main.success'),  $topReadBooks);
        } catch (Exception $th) {
            return $this->outApiJson('exception', $this->getExceptionMessage($th));
        }
    } 
    
    private function sendThanksMessage($user_id){
        $userData = User::find($user_id);
        $response = \Http::post('https://run.mocky.io/v3/'.env('SMS_APP_CODE'), [
            'name' => $userData->name,
            'phone' => $userData->phone_number,
            'message' => 'thanks for reading book'
        ]);
        return response()->json($response);
    }
}
