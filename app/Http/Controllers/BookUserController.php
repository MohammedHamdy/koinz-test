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

    public function mostReadBooks(): JsonResponse
    {
        try {
            $bookReads = \DB::select("
            with recursive  t as( 
                select book_id,start_page,max(end_page)end_page
                  ,row_number()over(partition by book_id order by start_page) rn
                from book_user
                group by book_id,start_page
              )
              ,r as(
                select 0 lvl,bu.book_id,bu.start_page,bu.end_page,bu.rn
                from t bu
                where not exists(select 1 from t bu2 
                       where bu2.book_id=bu.book_id and bu2.rn<bu.rn
                         and bu.start_page between bu2.start_page and bu2.end_page)
                union all
                select lvl+1,r.book_id,r.start_page,t.end_page,t.rn
                from r inner join t on t.book_id=r.book_id and t.rn>r.rn
                     and r.end_page between t.start_page and r.end_page
              )
              select book_id,book_name,sum(end_page-start_page+1) num_of_read_pages
              from (
                    select book_id,start_page,max(end_page) end_page 
                    from r 
                    group by book_id,start_page
                ) gr
                    JOIN books ON books.id=gr.book_id   
              group by book_id ORDER by num_of_read_pages DESC LIMIT 5;
            ");
            return $this->outApiJson('success', trans('main.success'),  $bookReads);
        } catch (Exception $th) {
            return $this->outApiJson('exception', $this->getExceptionMessage($th));
        }
    }    
}
