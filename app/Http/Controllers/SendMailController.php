<?php

namespace App\Http\Controllers;

use App\Mail\ContactPortfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendMailController extends Controller
{
    public function contact(Request $request){

        try {
            Mail::to($request->email)->send(new ContactPortfolio($request));
            Mail::to('no-reply@deprakoso.com')->send(new ContactPortfolio($request,2));
        } catch (\Throwable $th) {
            return response()->json($th,400);
        }
		return response()->json('OK');

	}
}
