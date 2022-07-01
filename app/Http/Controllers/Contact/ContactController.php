<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Notification;
use App\Notifications\ContactNotification;
use Validator;

class ContactController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['store']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if( $request->created_at ) {
            $contact = Contact::Orderby( $request->sortby , $request->orderby )->whereDate( 'created_at', $request->created_at )->paginate($request->paginate);
        }

        elseif( $request->updated_at ) {
            $contact = Contact::Orderby( $request->sortby , $request->orderby )->whereDate( 'updated_at', $request->updated_at )->paginate($request->paginate);
        }

        elseif( $request->date_from && $request->date_to ) {
            $contact = Contact::Orderby( $request->sortby , $request->orderby )->whereDate('created_at', [$request->date_from, $request->date_to])->paginate($request->paginate);
        }

        elseif( $request->expand ) {
            return new ContactResource(Contact::findOrFail($request->expand));
        }
        elseif( $request->filter ) {
            $contact = Contact::Orderby( $request->sortby , $request->orderby )->where( $request->filter, 'LIKE', "%$request->filtervalue%" )->get();
        } else {
            $contact = Contact::Orderby( $request->sortby , $request->orderby )->paginate($request->paginate);
        }
        
        
        
        return ContactResource::collection($contact);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required',
            'message' => 'required',
            'subject' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        // end validation

        $contact = new Contact();
        $contact->email = $request->email;
        $contact->name = $request->name;
        $contact->message = $request->message;
        $contact->subject = $request->subject;
        $contact->save();

        $offerData = [
            'name' => 'BOGO',
            'body' => 'You received an offer.',
            'thanks' => 'Thank you',
            'offerText' => 'Check out the offer',
            'offerUrl' => url('/'),
            'offer_id' => 007
        ];

        Notification::send($contact, new ContactNotification($contact));

        return "created";
    }

    // trashed
    public function trashed() {
        $contact = Contact::onlyTrashed()->get();
        return ContactResource::collection($contact);
    }

    // delete
    public function destroy($id) {
        $contact = Contact::withTrashed()->where('id', $id);
        $contact->delete();
        return 'delete';
    }

    // restore
    public function restore($id) {
        $contact = Contact::onlyTrashed()
        ->where('id', $id);
        $contact->restore();
        return 'restore';
    }

    // forced
    public function forced($id) {
        $contact = Contact::onlyTrashed()
        ->where('id', $id);
        $contact->forceDelete();
        return 'forced';
    }
}
