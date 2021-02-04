<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Image;
use Carbon\Carbon;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::latest()->paginate(15);
    }
     public function priceRange()
    {
        return Product::selectRaw("MIN(price) AS min, MAX(price) AS max")->get()->toArray();
    }
    function filterByPrice($value)
    {
        $val=explode(',', $value);
        return Product::whereBetween('price',$val)->paginate(15);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->data_validate($request);
        $get_last_product_insert_id =  Product::insertGetId([
           'name'=>$request->name,
           'price'=>$request->price,
           'detail'=>$request->detail,
           'expired_date'=>$request->expired_date
        ]);

        if ($request->image) {
            Product::where('id',$get_last_product_insert_id)->update([
                'image'=>$this->imageprocess($request->image),
            ]);
        }
        return response(['success'=>'Data inserted successfully'],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($id==1){
            $currentDate=Carbon::parse(now())->format('Y-m-d');
            return Product::whereDate('expired_date', '<', $currentDate)->paginate(15);
        }else{
            return $this->index();
        }

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
        $this->data_validate($request);
        $data=[
            'name'=>$request->name,
            'price'=>$request->price,
            'detail'=>$request->detail,
            'expired_date'=>$request->expired_date,
        ];
        $product =Product::find($id);
        if ($request->image && $product->image!=$request->image) {
            if ($product->image!='default.jpg') {
                unlink(base_path('public/images/products/'.$product->image));
            }
            $data+=['image'=>$this->imageprocess($request->image)];
        }
        Product::where('id',$id)->update($data);
        return response()->json(['success'=>'Data updated successfully'],201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product =  Product::find($id);
        if($product->image !='default.jpg'){
            unlink(base_path('public/images/products/'.$product->image));
        }
        $product->delete();
        return response(['success'=>'Data deleted successfully'],201);
    }

     public function imageprocess($image)
     {
        $exploed1 = explode(";", $image);
        $exploed2 = explode("/", $exploed1[0]);
        $filename =  time().'.'.$exploed2[1];

        Image::make($image)->resize(215, 215)->save(base_path('public/images/products/'.$filename),50);
        return $filename;
     }

    private function data_validate($request,$id=null)
    {
        $this->validate($request, [
            'name' => 'required',
            'price' => 'required',
            //'image'=>'bail|required|mimes:jpeg,png,jpg,gif,svg|max:2048',
            //'image' => 'required|file|image|size:1024|dimensions:max_width=500,max_height=500',
            'expired_date' => 'required',
            'detail' => 'required',
        ]);
    }

}
