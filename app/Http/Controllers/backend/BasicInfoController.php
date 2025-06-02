<?php

namespace App\Http\Controllers\backend;

use App\Models\BasicInfo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class BasicInfoController extends Controller
{  
    protected $breadcrumb;
    public function __construct(){$this->breadcrumb = ['title'=>'Settings'];}
    public function index()
    {
        $data['basicInfo'] = BasicInfo::first();
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.basic-infos.index', compact('data'));
    }

    public function edit($id)
    {
        $data['basicInfo'] = BasicInfo::find($id);
        $data['breadcrumb'] = $this->breadcrumb;
        return view('backend.basic-infos.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $basicInfo = BasicInfo::find($id);
        $data = $request->all();
        if(isset($data['logo'])){
            $fileName = 'logo-'. time().'.'. $data['logo']->getClientOriginalExtension();
            $data['logo']->move(public_path('uploads/basic-info'), $fileName);
            $data['logo'] = $fileName;

            $imagePath = public_path('uploads/basic-info/'.$basicInfo->logo);
            if($basicInfo->logo) unlink($imagePath);

        }else unset($data['logo']);

        if(isset($data['favicon'])){
            $fileName = 'favicon-'. time().'.'. $data['favicon']->getClientOriginalExtension();
            $data['favicon']->move(public_path('uploads/basic-info/'), $fileName);
            $data['favicon'] = $fileName;

            $imagePath = public_path('uploads/basic-info/'.$basicInfo->favicon);
            if($basicInfo->favicon) unlink($imagePath);

        }else unset($data['favicon']);

        $basicInfo->update($data);
        return redirect()->route('basic-infos.index')->with('alert',['messageType'=>'success','message'=>'Data Updated Successfully!']);

    }

}