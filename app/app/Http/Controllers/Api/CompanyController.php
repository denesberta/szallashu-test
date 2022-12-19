<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(Company::with('activity')->with('address')->with('user')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $company = new Company;
        $company->name = $request->input('name');
        $company->activityId = $request->input('activityId');
        $company->registrationNumber = $request->input('registrationNumber');
        $company->foundationDate = $request->input('foundationDate');
        $company->employees = $request->input('employees');
        $company->active = $request->input('active');

        try {
            $company->save();
            return response()->json($company, 201);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    /**
     * Show multiple companies
     *
     * @param string $ids
     * @return JsonResponse
     */
    public function show(string $ids): \Illuminate\Http\JsonResponse
    {
        $ids = explode(',', $ids);
        $company = Company::with('activity')->with('address')->with('user')->whereIn('id', $ids)->get();

        return response()->json($company);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $company = Company::find($id);
        $company->name = $request->input('name');
        $company->activityId = $request->input('activityId');
        $company->registrationNumber = $request->input('registrationNumber');
        $company->foundationDate = $request->input('foundationDate');
        $company->employees = $request->input('employees');
        $company->active = $request->input('active');

        try {
            $company->save();
            return response()->json($company, 201);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $company = Company::find($id);

        try {
            $company->delete();
            return response()->json($company, 201);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }
}
