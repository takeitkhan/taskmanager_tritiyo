<?php

namespace Tritiyo\Material\Controllers;

use Tritiyo\Material\Models\Material;
use Tritiyo\Material\Repositories\MaterialInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class MaterialController extends Controller
{
    /**
     * @var MaterialInterface
     */
    private $material;

    /**
     * RoutelistController constructor.
     * @param MaterialInterface $material
     */
    public function __construct(MaterialInterface $material)
    {
        $this->material = $material;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $materials = $this->material->getAll();
        return view('material::index', ['materials' => $materials]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('material::create');
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
            return redirect('materials.create')
                ->withErrors($validator)
                ->withInput();
        } else {
            // store
            $attributes = [
                'name' => $request->name,
                'unit' => $request->unit,
            ];

            try {
                $material = $this->material->create($attributes);
                return redirect(route('materials.index'))->with(['status' => 1, 'message' => 'Successfully created']);
            } catch (\Exception $e) {
                return view('material::create')->with(['status' => 0, 'message' => 'Error']);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \Tritiyo\material\Models\material $material
     * @return \Illuminate\Http\Response
     */
    public function show(material $material)
    {
        return view('material::show', ['material' => $material]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Tritiyo\material\Models\material $material
     * @return \Illuminate\Http\Response
     */
    public function edit(material $material)
    {
        return view('material::edit', ['material' => $material]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Tritiyo\material\Models\material $material
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, material $material)
    {
        // store
        $attributes = [
            'name' => $request->name,
            'unit' => $request->unit,
        ];

        try {
            $material = $this->material->update($material->id, $attributes);

            return back()
                ->with('message', 'Successfully saved')
                ->with('status', 1)
                ->with('material', $material);
        } catch (\Exception $e) {
            return view('material::edit', $material->id)->with(['status' => 0, 'message' => 'Error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Tritiyo\material\Models\material $material
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->material->delete($id);
        return redirect()->back()->with(['status' => 1, 'message' => 'Successfully deleted']);
    }

    /**
     * Search 
     * */
    public function search(Request $request) {

        if(!empty($request->key)) {
            $default = [
            'search_key' => $request->key ?? '',
            'limit' => 10,
            'offset' => 0
            ];        
            $materials = $this->material->getDataByFilter($default);            
        } else {
            $materials = $this->material->getAll();        
        }
        return view('material::index', ['materials' => $materials]);        
    }
}
