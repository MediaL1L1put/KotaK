<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skate;
use App\Models\SkateModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SkateController extends Controller
{
    public function index()
    {
        $skates = Skate::with('model')->paginate(20);
        return view('admin.skates.index', compact('skates'));
    }

    public function create()
    {
        $models = SkateModel::all();
        return view('admin.skates.create', compact('models'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'skate_model_id' => 'required|exists:skate_models,id',
            'size' => 'required|integer|min:30|max:48',
            'quantity' => 'required|integer|min:1',
            'price_per_hour' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Skate::create([
            'skate_model_id' => $request->skate_model_id,
            'size' => $request->size,
            'quantity' => $request->quantity,
            'available_quantity' => $request->quantity,
            'price_per_hour' => $request->price_per_hour
        ]);

        return redirect()->route('admin.skates.index')
            ->with('success', 'Коньки успешно добавлены');
    }

    public function edit(Skate $skate)
    {
        $models = SkateModel::all();
        return view('admin.skates.edit', compact('skate', 'models'));
    }

    public function update(Request $request, Skate $skate)
    {
        $validator = Validator::make($request->all(), [
            'skate_model_id' => 'required|exists:skate_models,id',
            'size' => 'required|integer|min:30|max:48',
            'quantity' => 'required|integer|min:0',
            'price_per_hour' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldQuantity = $skate->quantity;
        $newQuantity = $request->quantity;
        $difference = $newQuantity - $oldQuantity;

        $skate->update([
            'skate_model_id' => $request->skate_model_id,
            'size' => $request->size,
            'quantity' => $newQuantity,
            'available_quantity' => $skate->available_quantity + $difference,
            'price_per_hour' => $request->price_per_hour
        ]);

        return redirect()->route('admin.skates.index')
            ->with('success', 'Коньки успешно обновлены');
    }

    public function destroy(Skate $skate)
    {
        $skate->delete();
        return redirect()->route('admin.skates.index')
            ->with('success', 'Коньки удалены');
    }
}