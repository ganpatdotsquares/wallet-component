<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\EmailTemplate;
use Auth;
use Validator;

class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Retrieve the search query from the request
            $search = $request->input('search', ''); // Default to an empty string if no search query is provided

            // Query emails with optional search functionality
            $emails = EmailTemplate::sortable()
                ->latest()
                ->sortable()
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
                ->paginate(config('GET.admin_page_limit'))
                ->withQueryString();

            // Commit transaction (if any transactional operations are present)
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->route('admin.email')->with("status", $exception->getMessage());
        }

        return view('backend.email.index', compact('emails', 'search'));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request)
    {
        DB::beginTransaction();
        try {

            $email = EmailTemplate::findOrFail($request->id);
            DB::commit();
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            redirect()->route('admin.email.view')->with("status", $exception->getMessage());
        }
        return view('backend.email.show', compact('email'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editEmail(Request $request)
    {
        DB::beginTransaction();
        try {
            $email = EmailTemplate::findOrFail($request->id);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->route('admin.email.edit')->with('status', $exception->getMessage());
        }

        return view('backend.email.edit', compact('email'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {

            $requestData = $request->all();
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:60',
                'subject' => 'required|max:60',
                'eamil_description' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $email = EmailTemplate::findOrFail($request->id);
            $requestData = $request->all();
           
            $requestData['description'] = $requestData['eamil_description'];
            //$requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            $email->fill($requestData);

            $email->save();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.email')->with('success', 'Email has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmailTemplate $email)
    {
        try {
            $email->delete();
            $responce = ['status' => true, 'message' => 'This Email has been deleted successfully.', 'data' => $email];
        } catch (\Exception $e) {

            $responce = ['status' => false, 'message' => $e->getMessage()];
        }
        return $responce;
    }


    public function changeStatus(Request $request)
    {
        DB::beginTransaction();
        try {
            $email = EmailTemplate::find($request->id);
            $email->status = !$email->status;
            $email->save();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            redirect()->route('admin.email', $request->query())->with('status', 'Saved!');
        }
        return redirect()->route('admin.email')->with('status', 'Status updated successfully.');
    }
    public function create()
    {
        return view('backend.email.create');
    }

    public function save(Request $request)
    {
        DB::beginTransaction();
        try {
            // Create the new email template
            $emailTemplate = new EmailTemplate();
            $emailTemplate->name = $request->name;
            $emailTemplate->slug = $request->slug;
            $emailTemplate->subject = $request->subject;
            $emailTemplate->description = $request->description;
            $emailTemplate->status = $request->status;
            $emailTemplate->save();

            DB::commit();

            return redirect()->route('admin.email')->with('status', 'Email template created successfully.');
        } catch (\Exception $exception) {
            dd($exception);
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'An error occurred while saving the email template.']);
        }
    }

}
