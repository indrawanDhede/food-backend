<?php

namespace App\Http\Controllers;

use App\Http\Requests\FoodRequest;
use App\Models\Food;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|Response
     */
    public function index()
    {
        $food = Food::paginate(5);
        return view('food.index', ['food' => $food]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|Response
     */
    public function create()
    {
        return view('food.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FoodRequest $request
     * @return RedirectResponse
     */
    public function store(FoodRequest $request): RedirectResponse
    {
        $data = $request->all();
        if($request->file('picturePath')){
            $data['picturePath'] = $request->file('picturePath')->store('assets/food', 'public');
        }
        Food::create($data);
        return redirect()->route('food.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Food $food
     * @return Application|Factory|View|Response
     */
    public function edit(Food $food)
    {
        return view('food.edit', [
            'item' => $food
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FoodRequest $request
     * @param Food $food
     * @return RedirectResponse
     */
    public function update(FoodRequest $request, Food $food): RedirectResponse
    {
        $data = $request->all();

        if($request->file('picturePath')){
            $data['picturePath'] = $request->file('picturePath')->store('assets/food', 'public');
        }

        $food->update($data);
        return redirect()->route('food.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Food $food
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(Food $food): RedirectResponse
    {
         $food->delete();
         return redirect()->route('food.index');
    }
}
