<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;





class CategoryController extends Controller
{
    //获取所有分类
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }
    //保存
    public function store(Request $request)
    {
        $category = Category::create($request->all());
        return response()->json($category);
    }
    //显示
    public function show($id)
    {
        $category = Category::find($id);
        return response()->json($category);
    }
    //更新
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        $category->update($request->all());
        return response()->json($category);
    }
    //删除
    public function destroy($id)
    {
        $category = Category::find($id);
        $category->delete();
        return response()->json($category);
    }

}
