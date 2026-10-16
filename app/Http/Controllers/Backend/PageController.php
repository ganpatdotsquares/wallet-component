<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Validator;
use Illuminate\Support\Facades\DB;



class PageController extends Controller
{   
    /**
     * login a user using email & password.
     *
     * @return void
     */
    public function index(Request $request)
    {
        $search = $request->input('search', ''); 
        $pagesQuery = Page::query();
        if (!empty($search)) {
            $pagesQuery->where('title', 'LIKE', '%' . $search . '%')
                ->orWhere('description', 'LIKE', '%' . $search . '%')
                ->orWhere('short_description', 'LIKE', '%' . $search . '%');
        }

        $pages = $pagesQuery->sortable(['created_at' => 'desc'])->filter($request->query('keyword'))->paginate(config('GET.admin_page_limit'));
        return view('backend.pages.index', compact('pages','search'));
    }

    public function create()
    {
        return view('backend.pages.create');
    }
    public function store(Request $request)
    {
       
        try {
            $this->validate($request, [
                'title' => 'required|max:255',
                'short_description' => 'required',
                'slug' => 'max:255',
                'description' => 'required ',
            ]);
            $requestData = $request->all();
            $requestData['category'] = 'common';
          
            Page::create($requestData);
        } catch (\Illuminate\Database\QueryException $e) {
            dd($e);
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.page')->with('success', 'CMS page has been saved successfully');
    }

   /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        $page = Page::findOrFail($id);
        return view('backend.pages.view', compact('page'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = Page::findOrFail($id);
        return view('backend.pages.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $page = Page::findOrFail($id);
            $this->validate($request, [
                'title' => 'required|max:255',
                'short_description' => 'max:255',
                'description' => 'required',
            ]);

            $page->title = $request->input('title');
            $page->short_description = $request->input('short_description');
            $page->description = $request->input('description');
            $page->status = $request->has('status') ? '1' : '0';
          
            $page->save();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.page')->with('success', 'CMS page has been updated successfully.');
    }

    public function changestatus(Request $request)
    {
        DB::beginTransaction();
        try {
            $Page = Page::find($request->id);
            if($Page->status == 1){
                $Page->status = '0';
            }else{
                $Page->status = '1';
            }
            
            $Page->save();
            DB::commit();
        } catch (\Exception $exception){
            dd($exception);
            DB::rollBack();
            redirect()->route('admin.page')->with('success', 'Saved!'); 
        }
        return redirect()->route('admin.page')->with("status", "Status updated successfully.");
    }



}
