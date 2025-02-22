<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Models\Paste;
use App\Models\Syntax;
use Datatables;
use Illuminate\Http\Request;
use Validator;

class PasteController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pastes.index')->with('page_title', 'Pastes');
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $pastes = Paste::with('user')->select(['id', 'title', 'slug', 'user_id', 'syntax', 'expire_time', 'status', 'views', 'password', 'encrypted', 'created_at']);
            return Datatables::of($pastes)
                ->addColumn('check', function ($item) {
                    return '<input type="checkbox" class="check" name="check[]" value="' . $item->id . '">';
                })
                ->editColumn('title', function ($paste) {
                    return '<a href="' . $paste->url . '" target="_blank">' . $paste->title_f . '</a>';
                })
                ->addColumn('user', function ($paste) {
                    return (isset($paste->user)) ? '<a href="' . url('admin/users/' . $paste->user->id . '/edit') . '" target="_blank">' . $paste->user->name . '</a>' : 'Anonymous';
                })
                ->addColumn('status', function ($paste) {
                    if ($paste->status == 2) {
                        return '<span class="text-warning">Unlisted</span>';
                    } elseif ($paste->status == 1) {
                        return '<span class="text-success">Public</span>';
                    } else {
                        return '<span class="text-danger">Private</span>';
                    }

                })
                ->editColumn('encrypted', function ($paste) {
                    if ($paste->encrypted == 1) {
                        return '<span class="text-success">Yes</span>';
                    } else {
                        return '<span class="text-danger">No</span>';
                    }

                })
                ->addColumn('password_protected', function ($paste) {
                    if (!empty($paste->password)) {
                        return '<i class="fa fa-lock text-danger"></i>';
                    } else {
                        return '-';
                    }

                })
                ->editColumn('created_at', function ($item) {
                    return (!empty($item->created_at)) ? $item->created_at->format('Y-m-d H:i:s') : '-';
                })                  
                ->addColumn('action', function ($paste) {
                    return '<a class="btn btn-sm btn-default" href="' . url('admin/pastes/' . $paste->id . '/edit') . '"><i class="fa fa-edit"></i> Edit</a> <a class="btn btn-sm btn-danger" href="' . url('admin/pastes/' . $paste->id . '/delete') . '"><i class="fa fa-trash"></i> Delete</a>';
                })
                ->make(true);
        }
    }

    public function create()
    {
        $paste = new \stdClass();

        $paste->title = (!empty(\Auth::user()->default_paste->title)) ? \Auth::user()->default_paste->title : "";
        $paste->status = (!empty(\Auth::user()->default_paste->status)) ? \Auth::user()->default_paste->status : "";
        $paste->syntax = (!empty(\Auth::user()->default_paste->syntax)) ? \Auth::user()->default_paste->syntax : config('settings.default_syntax');
        $paste->expire = (!empty(\Auth::user()->default_paste->expire)) ? \Auth::user()->default_paste->expire : "";
        $paste->password = (!empty(\Auth::user()->default_paste->password)) ? \Auth::user()->default_paste->password : "";
        $paste->encrypted = (!empty(\Auth::user()->default_paste->encrypted)) ? \Auth::user()->default_paste->encrypted : "";
        $paste->folder_id = (!empty(\Auth::user()->default_paste->folder_id)) ? \Auth::user()->default_paste->folder_id : "";
        $paste->description = (!empty(\Auth::user()->default_paste->description)) ? \Auth::user()->default_paste->description : "";
        $paste->tags = (!empty(\Auth::user()->default_paste->tags)) ? \Auth::user()->default_paste->tags : "";


        return view('admin.pastes.create', compact('paste'))->with('page_title', 'Pastes');
    }

    public function edit($id)
    {
        $paste = Paste::findOrfail($id);
        if(isset($paste->user)) $folders = Folder::where('user_id',$paste->user->id)->get();
        else $folders = Folder::where('user_id',\Auth::user()->id)->get();

        if ($paste->storage == 2) {
            $paste->content = file_get_contents(ltrim($paste->content, '/'));
        }
        if ($paste->encrypted == 1) {
            $paste->content = decrypt($paste->content);
        }

        return view('admin.pastes.edit', compact('paste','folders'))->with('page_title', 'Pastes');
    }

    public function store(Request $request)
    {
        $title_required = 'nullable';
        if (config('settings.paste_title_required') == 1) $title_required = 'required';
        $validator = Validator::make($request->all(), [
            'content' => 'required|min:1',
            'folder_id' => 'nullable|exists:folders,id',
            'status' => 'required|numeric|in:1,2,3',
            'syntax' => 'nullable|exists:syntax,slug',
            'expire' => 'required|max:3|in:N,10M,1H,1D,1W,2W,1M,6M,1Y,SD',
            'title' => $title_required . '|max:80|eco_string',
            'password' => 'nullable|max:50|string',
            'description' => 'nullable|string|max:2000',
            'tags' => 'nullable|string|max:200',            
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)->withInput();
        }


        if(!empty($request->folder_id))
        {
            Folder::where('id',$request->folder_id)->where('user_id',\Auth::user()->id)->firstOrfail();
        }  

        if(!empty(config('settings.banned_words')))
        {
            $banned_words = explode(',',config('settings.banned_words'));
            foreach($banned_words as $banned_word){
                preg_match('/\b'.$banned_word.'\b/iu', $request->content, $matches, PREG_OFFSET_CAPTURE);
                if(count($matches) > 0){
                    return redirect()->back()->withErrors(__('Please remove') . ' "' . $banned_word . '" ' . __('word from your paste content'))->withInput();
                }
            }
        }

        $ip_address = request()->ip();

        $paste = new Paste();
        $paste->title = $request->title;
        $paste->description = $request->description;
        $paste->tags = $request->tags;        
        $paste->folder_id = $request->folder_id;
        $paste->syntax = (!empty($request->syntax)) ? $request->syntax : config('settings.default_syntax');

        switch ($request->expire) {
            case '10M':
                $expire = '10 minutes';
                break;

            case '1H':
                $expire = '1 hour';
                break;

            case '1D':
                $expire = '1 day';
                break;

            case '1W':
                $expire = '1 week';
                break;

            case '2W':
                $expire = '2 week';
                break;

            case '1M':
                $expire = '1 month';
                break;

            case '6M':
                $expire = '6 months';
                break;

            case '1Y':
                $expire = '1 year';
                break;

            case 'SD':
                $expire = 'SD';
                break;

            default:
                $expire = 'N';
                break;
        }

        if ($expire != 'N') {
            if ($expire == 'SD') $paste->self_destroy = 1;
            else $paste->expire_time = date('Y-m-d H:i:s', strtotime('+' . $expire));
        }

        $paste->status = $request->status;
        $paste->content = htmlentities($request->content);
        if (\Auth::check()) {
            $paste->user_id = \Auth::user()->id;
        }
        $paste->ip_address = $ip_address;

        if ($request->password) {
            $paste->password = \Hash::make($request->password);
        }

        if ($request->encrypted) {
            $paste->encrypted = 1;
            $paste->content = encrypt($request->content);

        } else {
            $paste->content = htmlentities($request->content);
        }


        if (config('settings.paste_storage') == 'file') {

            $paste->storage = 2;
            $content = $paste->content;
            if (\Auth::check()) $destination_path = 'uploads/users/' . \Auth::user()->name;
            else $destination_path = 'uploads/pastes/' . date('Y') . '/' . date('m') . '/' . date('d');
            if (!file_exists($destination_path)) {
                mkdir($destination_path, 0775, true);
            }
            $filename = str_random(20) . '.txt';
            file_put_contents($destination_path . '/' . $filename, $content);
            $paste->content = '/' . $destination_path . '/' . $filename;
        }

        $paste->save();

        if(auth()->check()) \Cache::forget('my_recent_pastes_'.auth()->user()->id);

        return redirect()->back()->withSuccess('Paste successfully created.');
    }

    public function update($id, Request $request)
    {
        $paste = Paste::where('id', $id)->firstOrfail();
        $title_required = 'nullable';
        if (config('settings.paste_title_required') == 1) $title_required = 'required';
        $validator = Validator::make($request->all(), [
            'content' => 'required|min:1',
            'folder_id' => 'nullable|exists:folders,id',
            'status' => 'required|numeric|in:1,2,3',
            'syntax' => 'nullable|exists:syntax,slug',
            'title' => $title_required . '|max:80|eco_string',
            'password' => 'nullable|max:50|string',
            'description' => 'nullable|string|max:2000',
            'tags' => 'nullable|string|max:200',            
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)->withInput();
        }

        if(!empty($request->folder_id) && !empty($paste->user))
        {
            Folder::where('id',$request->folder_id)->where('user_id',$paste->user->id)->firstOrfail();
        }  

        if(!empty(config('settings.banned_words')))
        {
            $banned_words = explode(',',config('settings.banned_words'));
            foreach($banned_words as $banned_word){
                preg_match('/\b'.$banned_word.'\b/iu', $request->content, $matches, PREG_OFFSET_CAPTURE);
                if(count($matches) > 0){
                    return redirect()->back()->withErrors(__('Please remove') . ' "' . $banned_word . '" ' . __('word from your paste content'))->withInput();
                }
            }
        }        

        $paste->title = $request->title;
        $paste->description = $request->description;
        $paste->tags = $request->tags;        
        $paste->folder_id = $request->folder_id;
        $paste->syntax = (!empty($request->syntax)) ? $request->syntax : config('settings.default_syntax');
        $paste->status = $request->status;

        if($request->remove_password){
            $paste->password = null;
        }

        if ($request->password) {
            $paste->password = \Hash::make($request->password);
        }

        if ($request->encrypted) {
            $paste->encrypted = 1;
            $paste->content = encrypt($request->content);

        } else {
            $paste->encrypted = 0;
            $paste->content = htmlentities($request->content);
        }


        if (config('settings.paste_storage') == 'file') {

            if ($paste->storage == 2) {
                if (file_exists(ltrim($paste->content, '/'))) {
                    unlink(ltrim($paste->content, '/'));
                }
            }

            $paste->storage = 2;
            $content = $paste->content;
            if (isset($paste->user)) $destination_path = 'uploads/users/' . $paste->user->name;
            else $destination_path = 'uploads/pastes/' . date('Y') . '/' . date('m') . '/' . date('d');
            if (!file_exists($destination_path)) {
                mkdir($destination_path, 0775, true);
            }
            $filename = str_random(20) . '.txt';
            file_put_contents($destination_path . '/' . $filename, $content);
            $paste->content = '/' . $destination_path . '/' . $filename;
        }

        $paste->save();

        if(auth()->check()) \Cache::forget('my_recent_pastes_'.auth()->user()->id);

        return redirect()->back()->withSuccess('Paste successfully updated.');
    }

    public function destroy($id)
    {
        $paste = Paste::where('id', $id)->firstOrfail();
        update_recent_pastes($paste->id);
        update_trendings($paste->id);
        $paste->delete();

        if(auth()->check()) \Cache::forget('my_recent_pastes_'.auth()->user()->id);

        return redirect('admin/pastes')->withSuccess('Paste Successfully deleted.');
    }

    public function deleteSelected(Request $request)
    {
        if (!empty($request->ids)) {
            $pastes = Paste::whereIn('id', $request->ids)->get(['id']);
            foreach ($pastes as $paste) {
                $p = Paste::where('id', $paste->id)->first();
                $p->delete();
            }
            \Cache::forget('recent_pastes');
            \Cache::forget('trending_today');
            \Cache::forget('trending_week');
            \Cache::forget('trending_month');
            \Cache::forget('trending_year');

            if(auth()->check()) \Cache::forget('my_recent_pastes_'.auth()->user()->id);
            
            echo "success";
        } else {
            echo "error";
        }
    }
}
