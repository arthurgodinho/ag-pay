<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MarketingController extends Controller
{
    /**
     * Exibe página de marketing (orderbumps, upsells, cupons)
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Busca produtos do usuário
        $products = DB::table('products')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Order Bumps
        $orderBumps = DB::table('order_bumps')
            ->join('products', 'order_bumps.product_id', '=', 'products.id')
            ->where('products.user_id', $user->id)
            ->select('order_bumps.*', 'products.name as product_name')
            ->orderBy('order_bumps.created_at', 'desc')
            ->get();
        
        // Upsells
        $upsells = collect([]);
        if (DB::getSchemaBuilder()->hasTable('upsells')) {
            $upsells = DB::table('upsells')
                ->join('products', 'upsells.product_id', '=', 'products.id')
                ->where('products.user_id', $user->id)
                ->select('upsells.*', 'products.name as product_name')
                ->orderBy('upsells.created_at', 'desc')
                ->get();
        }
        
        // Cupons
        $cupons = collect([]);
        if (DB::getSchemaBuilder()->hasTable('coupons')) {
            $cupons = DB::table('coupons')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('dashboard.marketing.index', compact('orderBumps', 'upsells', 'cupons', 'products'));
    }

    // ========== ORDER BUMPS ==========
    
    /**
     * Exibe formulário de edição de Order Bump
     */
    public function editOrderBump($id): View
    {
        $user = Auth::user();
        
        $orderBump = DB::table('order_bumps')
            ->join('products', 'order_bumps.product_id', '=', 'products.id')
            ->where('order_bumps.id', $id)
            ->where('products.user_id', $user->id)
            ->select('order_bumps.*', 'products.name as product_name')
            ->first();

        if (!$orderBump) {
            abort(404);
        }

        $products = DB::table('products')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('dashboard.marketing.orderbump-edit', compact('orderBump', 'products'));
    }

    /**
     * Atualiza Order Bump
     */
    public function updateOrderBump(Request $request, $id): RedirectResponse
    {
        $user = Auth::user();
        
        $orderBump = DB::table('order_bumps')
            ->join('products', 'order_bumps.product_id', '=', 'products.id')
            ->where('order_bumps.id', $id)
            ->where('products.user_id', $user->id)
            ->first();

        if (!$orderBump) {
            return back()->with('error', 'Order Bump não encontrado');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value_from' => 'nullable|numeric|min:0',
            'value_for' => 'required|numeric|min:0.01',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        // Verifica se o produto pertence ao usuário
        $product = DB::table('products')
            ->where('id', $request->product_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$product) {
            return back()->with('error', 'Produto não encontrado ou não pertence a você')->withInput();
        }

        try {
            $imagePath = $orderBump->image;
            if ($request->hasFile('image')) {
                // Remove imagem antiga
                if ($orderBump->image && file_exists(public_path($orderBump->image))) {
                    unlink(public_path($orderBump->image));
                }
                
                $image = $request->file('image');
                $imageName = 'orderbump_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = 'IMG/order-bumps/' . $imageName;
                $image->move(public_path('IMG/order-bumps'), $imageName);
            }

            DB::table('order_bumps')
                ->where('id', $id)
                ->update([
                    'product_id' => $request->product_id,
                    'name' => $request->name,
                    'description' => $request->description ?? null,
                    'value_from' => $request->value_from ?? null,
                    'value_for' => $request->value_for,
                    'image' => $imagePath,
                    'is_active' => $request->has('is_active'),
                    'updated_at' => now(),
                ]);

            return redirect()->route('dashboard.marketing.index')
                ->with('success', 'Order Bump atualizado com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar Order Bump', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            return back()->with('error', 'Erro ao atualizar Order Bump: ' . $e->getMessage())->withInput();
        }
    }

    // ========== UPSELLS ==========
    
    /**
     * Exibe formulário de edição de Upsell
     */
    public function editUpsell($id): View
    {
        $user = Auth::user();
        
        $upsell = DB::table('upsells')
            ->join('products', 'upsells.product_id', '=', 'products.id')
            ->where('upsells.id', $id)
            ->where('products.user_id', $user->id)
            ->select('upsells.*', 'products.name as product_name')
            ->first();

        if (!$upsell) {
            abort(404);
        }

        $products = DB::table('products')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('dashboard.marketing.upsell-edit', compact('upsell', 'products'));
    }

    /**
     * Atualiza Upsell
     */
    public function updateUpsell(Request $request, $id): RedirectResponse
    {
        $user = Auth::user();
        
        $upsell = DB::table('upsells')
            ->join('products', 'upsells.product_id', '=', 'products.id')
            ->where('upsells.id', $id)
            ->where('products.user_id', $user->id)
            ->first();

        if (!$upsell) {
            return back()->with('error', 'Upsell não encontrado');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'upsell_product_id' => 'required|exists:products,id|different:product_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        // Verifica se os produtos pertencem ao usuário
        $product = DB::table('products')
            ->where('id', $request->product_id)
            ->where('user_id', $user->id)
            ->first();

        $upsellProduct = DB::table('products')
            ->where('id', $request->upsell_product_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$product || !$upsellProduct) {
            return back()->with('error', 'Produto não encontrado ou não pertence a você')->withInput();
        }

        if ($request->product_id == $request->upsell_product_id) {
            return back()->with('error', 'O produto gatilho e o produto oferta não podem ser o mesmo')->withInput();
        }

        try {
            $imagePath = $upsell->image;
            if ($request->hasFile('image')) {
                // Remove imagem antiga
                if ($upsell->image && file_exists(public_path($upsell->image))) {
                    unlink(public_path($upsell->image));
                }
                
                $image = $request->file('image');
                $imageName = 'upsell_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = 'IMG/upsells/' . $imageName;
                $image->move(public_path('IMG/upsells'), $imageName);
            }

            DB::table('upsells')
                ->where('id', $id)
                ->update([
                    'product_id' => $request->product_id,
                    'upsell_product_id' => $request->upsell_product_id,
                    'title' => $request->title,
                    'description' => $request->description ?? null,
                    'price' => $request->price,
                    'image' => $imagePath,
                    'is_active' => $request->has('is_active'),
                    'updated_at' => now(),
                ]);

            return redirect()->route('dashboard.marketing.index')
                ->with('success', 'Upsell atualizado com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar Upsell', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            return back()->with('error', 'Erro ao atualizar Upsell: ' . $e->getMessage())->withInput();
        }
    }

    // ========== CUPONS ==========
    
    /**
     * Exibe formulário de edição de Cupom
     */
    public function editCoupon($id): View
    {
        $user = Auth::user();
        
        $coupon = DB::table('coupons')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$coupon) {
            abort(404);
        }

        return view('dashboard.marketing.coupon-edit', compact('coupon'));
    }

    /**
     * Atualiza Cupom
     */
    public function updateCoupon(Request $request, $id): RedirectResponse
    {
        $user = Auth::user();
        
        $coupon = DB::table('coupons')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$coupon) {
            return back()->with('error', 'Cupom não encontrado');
        }

        $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($user, $id) {
                    $exists = DB::table('coupons')
                        ->where('code', strtoupper($value))
                        ->where('user_id', $user->id)
                        ->where('id', '!=', $id)
                        ->exists();
                    if ($exists) {
                        $fail('Este código de cupom já existe para sua conta.');
                    }
                },
            ],
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0.01',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
        ]);

        try {
            DB::table('coupons')
                ->where('id', $id)
                ->update([
                    'code' => strtoupper($request->code),
                    'type' => $request->type,
                    'value' => $request->value,
                    'usage_limit' => $request->usage_limit,
                    'valid_from' => $request->valid_from ? \Carbon\Carbon::parse($request->valid_from) : null,
                    'valid_until' => $request->valid_until ? \Carbon\Carbon::parse($request->valid_until) : null,
                    'is_active' => $request->has('is_active'),
                    'updated_at' => now(),
                ]);

            return redirect()->route('dashboard.marketing.index')
                ->with('success', 'Cupom atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar cupom: ' . $e->getMessage())->withInput();
        }
    }
}

