<?php

namespace App\Http\Controllers\Api;

use App\Events\PurchaseMade;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * API Controller for purchases (demo/testing purposes).
 */
class PurchaseController extends Controller
{
    /**
     * Get user's purchase history.
     */
    public function index(User $user): JsonResponse
    {
        $purchases = $user->purchases()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $purchases,
        ]);
    }

    /**
     * Simulate a purchase for testing.
     * 
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function store(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'product_name' => 'nullable|string|max:255',
        ]);

        // Create the purchase
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'product_name' => $validated['product_name'] ?? 'Demo Product',
            'transaction_id' => 'PUR_' . strtoupper(Str::random(8)),
        ]);

        // Fire PurchaseMade event - this triggers the achievement processing
        event(new PurchaseMade($user, $purchase));

        return response()->json([
            'success' => true,
            'message' => 'Purchase recorded successfully',
            'data' => [
                'purchase' => $purchase,
                'total_purchases' => $user->purchases()->count(),
            ],
        ], 201);
    }

    /**
     * Reset demo progress for a user.
     *
     * Clears purchases, unlocked achievements, and cashback records so
     * the demo flow can be replayed from a clean state.
     */
    public function resetProgress(User $user): JsonResponse
    {
        if (!app()->environment('local')) {
            return response()->json([
                'success' => false,
                'message' => 'Reset is only available in local environment.',
            ], 403);
        }

        DB::transaction(function () use ($user): void {
            $user->purchases()->delete();
            $user->achievements()->detach();
            $user->cashbackPayments()->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'User demo progress reset successfully.',
            'data' => [
                'user_id' => $user->id,
                'total_purchases' => 0,
                'total_achievements' => 0,
                'total_cashback_earned' => 0,
            ],
        ]);
    }

}
