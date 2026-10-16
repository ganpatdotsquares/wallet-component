<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\SmtpMail;
use Illuminate\Support\Facades\DB;
use Exception;

class SmtpMailController extends Controller
{
    public function edit()
    {
        $smtpMail = SmtpMail::find(1); // Assuming there's only one record or use other means to get the right record
        return view('backend.smtp_mails.edit', compact('smtpMail'));
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $smtpMail = SmtpMail::findOrFail(1); // Assuming there's only one record or use other means to get the right record

            $smtpMail->host_name = $request->host_name;
            $smtpMail->username = $request->username;
            $smtpMail->password = $request->password; // Consider hashing or encrypting the password
            $smtpMail->port = $request->port;
            $smtpMail->smtp_tls = $request->has('smtp_tls') ? true : false;
            $smtpMail->status = $request->status ? true : false;

            $smtpMail->save();
            DB::commit();

            return redirect()->route('smtp_mails.edit')->with('status', 'SMTP settings updated successfully.');
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->route('smtp_mails.edit')->with('status', $exception->getMessage());
        }
    }
}
