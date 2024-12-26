<?php

namespace Tritiyo\Vehicle\Controllers;

use Tritiyo\Vehicle\Models\Vehicle;
use Tritiyo\Vehicle\Repositories\VehicleInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class VehicleController extends Controller
{
    /**
     * @var VehicleInterface
     */
    private $vehicle;

    /**
     * RoutelistController constructor.
     * @param VehicleInterface $vehicle
     */
    public function __construct(VehicleInterface $vehicle)
    {
        $this->vehicle = $vehicle;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicles = $this->vehicle->getAll();
        return view('vehicle::index', ['vehicles' => $vehicles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vehicle::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
            ]
        );

        // process the login
        if ($validator->fails()) {
            return redirect('vehicles.create')
                ->withErrors($validator)
                ->withInput();
        } else {
            // store
            $attributes = [
                'name' => $request->name,
                'size' => $request->size,
                'probably_cost' => $request->probably_cost,
            ];

            try {
                $vehicle = $this->vehicle->create($attributes);
                return redirect(route('vehicles.index'))->with(['status' => 1, 'message' => 'Successfully created']);
            } catch (\Exception $e) {
                return view('vehicle::create')->with(['status' => 0, 'message' => 'Error']);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \Tritiyo\Vehicle\Models\Vehicle $vehicle
     * @return \Illuminate\Http\Response
     */
    public function show(Vehicle $vehicle)
    {
        return view('vehicle::show', ['vehicle' => $vehicle]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Tritiyo\Vehicle\Models\Vehicle $vehicle
     * @return \Illuminate\Http\Response
     */
    public function edit(Vehicle $vehicle)
    {
        return view('vehicle::edit', ['vehicle' => $vehicle]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Tritiyo\Vehicle\Models\Vehicle $vehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        // store
        $attributes = [
            'name' => $request->name,
            'size' => $request->size,
            'probably_cost' => $request->probably_cost,
        ];

        try {
            $vehicle = $this->vehicle->update($vehicle->id, $attributes);

            return back()
                ->with('message', 'Successfully saved')
                ->with('status', 1)
                ->with('vehicle', $vehicle);
        } catch (\Exception $e) {
            return view('vehicle::edit', $vehicle->id)->with(['status' => 0, 'message' => 'Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Tritiyo\Vehicle\Models\Vehicle $vehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->vehicle->delete($id);
        return redirect()->back()->with(['status' => 1, 'message' => 'Successfully deleted']);
    }


    /**
     * Search
     */

    public function search(Request $request) {

        if(!empty($request->key)) {
            $default = [
                'search_key' => $request->key ?? '',
                'limit' => 10,
                'offset' => 0
            ];        
            $vehicles = $this->vehicle->getDataByFilter($default);            
        } else {
            $vehicles = $this->vehicle->getAll();        
        }
        return view('vehicle::index', ['vehicles' => $vehicles]);        
    }
}
