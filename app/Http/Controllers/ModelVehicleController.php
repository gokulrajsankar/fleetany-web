<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\ModelVehicleRepositoryEloquent;
use App\Entities\ModelVehicle;
use App\Entities\TypeVehicle;
use Log;
use Input;
use Lang;
use Session;
use Redirect;
use Response;
use Illuminate\Support\Facades\View;
use Prettus\Validator\Exceptions\ValidatorException;

class ModelVehicleController extends Controller
{

    protected $repository;
    
    public function __construct(ModelVehicleRepositoryEloquent $repository) 
    {
        $this->middleware('auth');
        $this->repository = $repository;
    }    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $modelvehicles = $this->repository->all();
        $typevehicle = TypeVehicle::lists('name','id');
        if(Request::isJson()) {
            return $modelvehicles;
        }

        return View::make("modelvehicle.index", compact('modelvehicles','typevehicle'));        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $modelvehicle = new ModelVehicle();
        //$typevehicle = TypeVehicle::all();
        $typevehicle = TypeVehicle::lists('name','id');
        return view("modelvehicle.edit", compact('modelvehicle','typevehicle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $this->repository->validator();
            $this->repository->create( Input::all() );
            Session::flash('message', Lang::get('general.succefullcreate', 
                  ['table'=> Lang::get('general.ModelVehicle')]));
            return Redirect::to('modelvehicle');
        } catch (ValidatorException $e) {
            return Redirect::back()->withInput()
                   ->with('errors',  $e->getMessageBag());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $modelvehicle= $this->repository->find($id);
        $typevehicle = TypeVehicle::lists('name','id');
        return View::make("modelvehicle.show", compact('modelvehicle','typevehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $modelvehicle = $this->repository->find($id);
        $typevehicle = TypeVehicle::lists('name','id');
        return View::make("modelvehicle.edit", compact('modelvehicle','typevehicle'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $this->repository->validator();
            $this->repository->update(Input::all(), $id);
            Session::flash('message', Lang::get('general.succefullupdate', 
                       ['table'=> Lang::get('general.ModelVehicle')]));
            return Redirect::to('modelvehicle');
         }
         catch (ValidatorException $e) {
            return Redirect::back()->withInput()
                    ->with('errors',  $e->getMessageBag());
         }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        Log::info('Delete field: '.$id);
        if($this->repository->find($id)) {
            $this->repository->delete($id);
            Session::flash('message', Lang::get("general.deletedregister"));
         }

        return Redirect::to('modelvehicle');
    }
}