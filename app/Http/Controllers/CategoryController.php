<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Feature;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\ApiResponse;

class CategoryController extends Controller
{
    public function index(Request $request){
   
        $dataList = Category::get();
      return response()->json($dataList);

   
   }

   public function dataInfoAddOrUpdate(Request $request){
    try{
        DB::beginTransaction();

         $dataInfo = Category::find($request->dataId);
         if(empty($dataInfo)){
            $dataInfo =new Category();
            $dataInfo->name = $request->catName;
            $dataInfo->save();
            if($dataInfo->save()){
               
                DB::commit();
                    return ApiResponse::formatSuccessResponseInsert();
            }else{

                DB::commit();
                return ApiResponse::formatFailedResponseInsert();
            }
           

         }else{
               $dataInfo = Category::find($request->dataId);
               $dataInfo->name = $request->catName;
               $dataInfo->save();
               if($dataInfo->save()){
               
                DB::commit();
                return ApiResponse::formatSuccessResponseUpdate();
               }else{
   
                DB::commit();
                return ApiResponse::formatFailedResponseUpdate();
               }
              
   
         }
     
         
           

      


    }catch(\Exception $err){
        DB::rollBack();
        return ApiResponse::formatErrorResponse($err);
    }
   }

   public function dataInfoDelete(Request $request){
    try{
        DB::beginTransaction();

        $dataInfo = Category::find($request->dataId);
        $categoryProduct= CategoryProduct::where('category_id',$request->dataId)->first();
        if(!empty($categoryProduct)){
            $deleteCatPro = CategoryProduct::where('category_id',$request->dataId)->get();
            foreach($deleteCatPro as $pro){
             
               $pro->delete();
            }
         }
        
        $product = Product::get();
        foreach($product as $pro){
            $deletePro = CategoryProduct::where('product_id',$pro->id)->first();
            
            if(empty($deletePro)){
                $featureProd = Feature::where('product_id',$pro->id)->get();
                foreach($featureProd as $proFeature){
                    $proFeature->delete();
                }
                $pro->delete();
            }
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
