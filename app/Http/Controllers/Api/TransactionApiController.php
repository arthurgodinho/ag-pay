<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TransactionApiController extends Controller
{
    /**
     * Lista transações do usuário
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->get('api_user');

        $query = Transaction::where('user_id', $user->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $transactions->items(),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }

    /**
     * Busca uma transação específica
     */
    public function show(string $uuid): JsonResponse
    {
        $user = request()->get('api_user');
        $transaction = Transaction::where('uuid', $uuid)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'uuid' => $transaction->uuid,
                'amount' => $transaction->amount_gross,
                'fee' => $transaction->fee,
                'amount_net' => $transaction->amount_net,
                'type' => $transaction->type,
                'status' => $transaction->status,
                'gateway_provider' => $transaction->gateway_provider,
                'external_id' => $transaction->external_id,
                'description' => $transaction->description,
                'created_at' => $transaction->created_at->toISOString(),
                'updated_at' => $transaction->updated_at->toISOString(),
            ],
        ]);
    }
}
