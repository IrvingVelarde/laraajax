<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Contact;
use Barryvdh\DomPDF\Facade as PDF;
use App\Exports\ContactsExport;
use Maatwebsite\Excel\Facades\Excel;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* $data = [
            'name'  => $request['name'],
            'email' => $request['email']
        ];
        return Contact::create($data); */
        $input = $request->all();
        $input['photo'] = null;
        if ($request->hasFile('photo')){
            $input['photo'] = '/upload/photo/'.str_slug($input['name'], '-').'.'.$request->photo->getClientOriginalExtension();
            $request->photo->move(public_path('/upload/photo/'), $input['photo']);
        }
        Contact::create($input);
        return response()->json([
            'success' => true,
            'message' => 'Contact Created'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $contact = Contact::find($id);
        return $contact;
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
        /* $contact = Contact::find($id);
        $contact->name  = $request['name'];
        $contact->email = $request['email'];
        $contact->update();
        return $contact;*/
        $input = $request->all();
        $contact = Contact::findOrFail($id);
        $input['photo'] = $contact->photo;
        if ($request->hasFile('photo')){
            if (!$contact->photo == NULL){
                unlink(public_path($contact->photo));
            }
            $input['photo'] = '/upload/photo/'.str_slug($input['name'], '-').'.'.$request->photo->getClientOriginalExtension();
            $request->photo->move(public_path('/upload/photo/'), $input['photo']);
        }
        $contact->update($input);
        return response()->json([
            'success' => true,
            'message' => 'Contact Updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Contact::destroy($id);
        $contact = Contact::findOrFail($id);
        if (!$contact->photo == NULL){
            unlink(public_path($contact->photo));
        }
        Contact::destroy($id);
        return response()->json([
            'success' => true,
            'message' => 'Contact Deleted'
        ]);
    }
    public function apiContact()
    {
        $contact = Contact::all();
 
        return Datatables::of($contact)
        ->addColumn('show_photo', function($contact){
                if ($contact->photo == NULL){
                    return 'No Image';
                }
                return '<img class="rounded-square" width="50" height="50" src="'. url($contact->photo) .'" alt="">';
        })
        ->addColumn('action', function($contact){
                return '<a href="#" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-eye-open"></i> Ver</a> ' .
                       '<a onclick="editForm('. $contact->id .')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Editar</a> ' .
                       '<a onclick="deleteData('. $contact->id .')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Eliminar</a>';
        })
        ->rawColumns(['show_photo', 'action'])->make(true);
    }

    public function exportPDF(){
        $contacts = Contact::limit(20)->get();
        $pdf = PDF::loadView('pdf',compact('contacts'));
        $pdf->setPaper('a4', 'potrait');
        return $pdf->stream('my_report_contacts.pdf');
        //return $pdf->download('my_report_contacts.pdf');
        //return view('pdf',compact('contacts'));
    }
    
    public function exportEXCEL(){
        /* VERSION 2.* de EXPORTAR A EXCEL 
        $contact = Contact::select('name','email')->get();
        return Excel::create('Laravel_Excel', function($excel) use ($contact){
            $excel->sheet('mysheet',function($sheet) use ($contact){               
                    $sheet->fromArray($contact);
            });
        })->download('xls'); */

        /* $contact = Contact::select('id', 'name', 'email', 'created_at')->get();
        Excel::create('contact', function($excel) use($contact) {
            $excel->sheet('Sheet 1', function($sheet) use($contact) {
                $sheet->fromArray($contact);
            });
        })->download('xls'); */
        return Excel::download(new ContactsExport, 'contacts.xlsx');
    }
}
