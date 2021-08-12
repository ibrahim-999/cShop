<?php

namespace App\Http\Controllers\Admin;

use App\Banner;
use Session;
use Image;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BannersController extends Controller
{
    public function banners()
    {
        Session::put('page', 'banners');
        $banners = Banner::get()->toArray();
        /*dd($banners); die;*/
        return view('admin.banners.banners')->with(compact('banners'));
    }

    public function updateBannerStatus(Request $request)
    {
        if($request->ajax()){
            $data = $request->all();

            if($data['status']=="Active")
            {
                $status = 0;
            }else{
                $status= 1;
            }
            Banner::where('id',$data['banner_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'banner_id'=>$data['banner_id']]);
        }
    }

    public function addeditBanner($id = null, Request $request)
    {
        if($id == "")
        {
            //Add Banner
            $title = "Add Banner";
            $banner = new Banner();
            $message = "Banner has been added successfully!";
        }
        else {
            // Edit Banner
            $title = "Edit Banner";
            $banner = Banner::find($id);
            $message= "Banner has been updated successfully!";
        }

        if($request->isMethod('post'))
        {
            $data = $request->all();
            $banner->link = $data['link'];
            $banner->title = $data['title'];
            $banner->alt = $data['alt'];
            //Upload Banner image
            if($request->hasFile('image'))
            {
                $image_tmp = $request->file('image');
                if($image_tmp->isValid())
                {
                    // Get Original image name
                    $image_name = $image_tmp->getClientOriginalName();
                    //Get Image extension
                    $extension = $image_tmp->getClientOriginalExtension();
                    // Generate new image name
                    $imageName = $image_name.'.'.rand(111,99999).'.'.$extension;
                    //Set path for every size
                    $banner_image_path = 'images/banner_images/'.$imageName;
                    //Upload Banner Image after resize
                    Image::make($image_tmp)->resize(1170,480)->save($banner_image_path);
                    //Save image in the Banner table
                    $banner->image = $imageName;
                }
            }
            $banner->save();
            session()->flash('success_message',$message);
            return redirect('admin/banners');
        }
        return view('admin.banners.add_edit_banner')->with(compact('title','banner'));
    }

    public function deleteBanner($id)
    {
        //Get Banner Image
        $bannerImage = Banner::where('id',$id)->first();

        //Get Banner Image Path
        $banner_image_path = 'images/banner_images';

        //Delete Banner Image if it is exists in Banner Folder
        if(file_exists($banner_image_path.$bannerImage->image))
        {
            unlink($banner_image_path.$bannerImage);
        }
        //Delete form Table
        Banner::where('id',$id)->delete();
        session()->flash('success_message','Banner has been delete!');
        return redirect()->back();
    }
}
