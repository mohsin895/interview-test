<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Feature;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
  public function getCagetgory(){
    $categories = Category::all();
    return response()->json([
        'categories'=>$categories,
    ],200);
  }
  
  public function getProduct(){
    $products = Product::with('categoryProducts','categoryProducts.categories')->get();
     return response()->json([
        'products'=>$products,
     ],200);
  }

  public function productInsert(Request $request){
    try{
        DB::beginTransaction();

        $dataInfo = Product::find($request->dataId);

        if(empty($dataInfo)){
            $dataInfo = new Product();
            $dataInfo->name = $request->productName;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = str_replace(' ', '_', $request->productName) . "_" . uniqid() . "." . $image->getClientOriginalExtension();
                $directory = 'product';

                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }
                $image->storeAs($directory, $imageName, 'public');
                $dataInfo->image = 'storage/' . $directory . '/' . $imageName;
            }

            $dataInfo->save();

       
            $this->insertCategory($request, $dataInfo) && $this->insertFeature($request, $dataInfo);
           

            DB::commit();
            return ApiResponse::formatSuccessResponseInsert();
        } else {
            DB::rollBack();
            return ApiResponse::formatFailedResponseInsert();
        }

    } catch(\Exception $err) {
        DB::rollBack();
        
        return ApiResponse::formatErrorResponse($err);
    }
}

public function insertCategory($request, $dataInfo)
{
    
    $categoryIds = json_decode($request->catId, true);

        foreach ($categoryIds as $catId) {
            CategoryProduct::create([
                'product_id' => $dataInfo->id,
                'category_id' => $catId
            ]);
        }
        return true;
    
}

public function insertFeature($request, $dataInfo)
{
    
    if ($request->has('feature') && is_array($request->feature)) {
        foreach ($request->feature as $feature) {
            if (!empty($feature)) {
                $featureInfo = new Feature();
                $featureInfo->product_id = $dataInfo->id;
                $featureInfo->description = $feature;
                $featureInfo->save();
            }
        }
    }
        return true;
    
}

public function productInfo(Request $request){
    $productInfo = Product::with('categoryProducts','categoryProducts.categories','feature')->find($request->dataId);
    return response()->json([
        'productInfo'=>$productInfo,
    ],200);
  
}

public function productUpdate(Request $request){
    try{
        DB::beginTransaction();

        $dataInfo = Product::find($request->dataId);

        if(!empty($dataInfo)){
           $imageInfo = Product::where('id',$request->dataId)->first();
            $dataInfo->name = $request->productName;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = str_replace(' ', '_', $request->productName) . "_" . uniqid() . "." . $image->getClientOriginalExtension();
                $directory = 'product';

                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }
                $image->storeAs($directory, $imageName, 'public');
                $dataInfo->image = 'storage/' . $directory . '/' . $imageName;
            } else {
               
                $dataInfo->image = $imageInfo->image;
            }

           

            $dataInfo->save();

       
         
           

            DB::commit();
            return ApiResponse::formatSuccessResponseInsert();
        } else {
            DB::rollBack();
            return ApiResponse::formatFailedResponseInsert();
        }

    } catch(\Exception $err) {
        DB::rollBack();
        
        return ApiResponse::formatErrorResponse($err);
    }
}
public function dataInfoDelete(Request $request){
    try{
        DB::beginTransaction();

        $dataInfo = Product::find($request->dataId);
        $categoryProduct= CategoryProduct::where('product_id',$request->dataId)->first();
        if(!empty($categoryProduct)){
            $deleteCatPro = CategoryProduct::where('product_id',$request->dataId)->get();
            foreach($deleteCatPro as $pro){
             
               $pro->delete();
            }
         }
        
        $product = Feature::where('product_id',$request->dataId)->get();
        foreach($product as $pro){
            
                $pro->delete();
            
        }
       
        if(empty($dataInfo)){
            return ApiResponse::formatDataNotFound();
        }


        if($dataInfo->delete()){
            DB::commit();
            return ApiResponse::formatSuccessResponseDelete();
        } else {
            DB::rollBack();
            return ApiResponse::formatFailedResponseDelete();   
             }
    } catch(\Exception $err){
     
        DB::rollBack();
        return ApiResponse::formatErrorResponse($err);
    }
}
}
