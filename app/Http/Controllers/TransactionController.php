<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|Response
     */
    public function index()
    {
        $transaction = Transaction::with(['user', 'food'])->paginate(10);
        return view('transaction.index', ['transaction' => $transaction]);
    }

    /**
     * Display the specified resource.
     *
     * @param Transaction $transaction
     * @return Application|Factory|View|Response
     */
    public function show(Transaction $transaction)
    {
      return view('transaction.detail', ['item' => $transaction]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $transaction = Transaction::find($id);
        $transaction->delete();

        return redirect()->route('transaction.index');
    }

    public function changeStatus(Request $request, $id, $status): RedirectResponse
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->status = $status;
        $transaction->save();

        return redirect()->route('transaction.show', $id);
    }
}
