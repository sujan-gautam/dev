<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Datatables;
use Illuminate\Http\Request;
use Validator;

class PageController extends Controller
{
    public function index()
    {
        return view('admin.page.index')->with('page_title', 'Pages');
    }

    public function create()
    {
        return view('admin.page.create')->with('page_title', 'Pages');
    }

    public function edit($id)
    {
        $page = Page::findOrfail($id);

        return view('admin.page.edit', compact('page'))->with('page_title', __('Pages'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|min:2|max:40|string',
            'content' => 'required',
            'active'  => 'required|numeric|in:0,1',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        } else {
            $page          = new Page();
            $page->title   = $request->title;
            $page->slug    = str_slug($request->title);
            $page->content = htmlentities($request->content);
            $page->active  = $request->active;
            $page->save();

            \Cache::forget('pages_menu');

            return redirect()->back()->withSuccess('Successfully added.');
        }
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|min:2|max:40|string',
            'content' => 'required',
            'active'  => 'required|numeric|in:0,1',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        } else {
            $page          = Page::findOrfail($id);
            $page->title   = $request->title;
            $page->content = htmlentities($request->content);
            $page->active  = $request->active;
            $page->save();

            \Cache::forget('page_'.$page->slug);
            \Cache::forget('pages_menu');

            return redirect()->back()->withSuccess('Successfully updated.');
        }
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $pages = Page::select(['id', 'title', 'slug', 'active']);
            return Datatables::of($pages)
                ->editColumn('title',function($page){
                    return $page->title;
                })
                ->editColumn('active', function ($page) {
                    if ($page->active == 1) {
                        return 'Yes';
                    } else {
                        return 'No';
                    }

                })
                ->addColumn('action', function ($page) {
                    return '<a class="btn btn-sm btn-default" href="' . url('admin/pages/' . $page->id . '/edit') . '"><i class="fa fa-edit"></i> Edit</a> <a class="btn btn-sm btn-danger" href="' . url('admin/pages/' . $page->id . '/delete') . '"><i class="fa fa-trash"></i> Delete</a>';
                })
                ->make(true);
        }
    }

    public function destroy($id)
    {
        $page = Page::where('id', $id)->firstOrfail();
        \Cache::forget('page_'.$page->slug);
        $page->delete();

        \Cache::forget('pages_menu');
        
        return redirect()->back()->withSuccess('Page Successfully deleted.');
    }
}
