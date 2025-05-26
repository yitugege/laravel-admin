<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    //获取所有产品
    public function index(Request $request)
    {

        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 100);
        $products = Product::paginate($perPage, ['*'], 'page', $page);
        Log::info('获取第' . $page . '页,每页' . $perPage . '条产品');
        return response()->json([
            'code' => 0,
            'data' => $products,
        ]);
    }
    //保存
    public function store(Request $request)
    {
        $product = Product::create($request->all());
        return response()->json($product);
    }
    //显示
    public function show($id)
    {
        $product = Product::find($id);
        return response()->json($product);
    }
    //更新
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $product->update($request->all());
        return response()->json($product);
    }
    //删除
    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();
        return response()->json($product);
    }
}
