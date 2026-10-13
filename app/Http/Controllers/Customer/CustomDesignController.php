<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomDesign;
use App\Services\CartService;
use App\Services\CustomDesignService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomDesignController extends Controller
{
    public function __construct(
        private CustomDesignService $designService,
        private CartService $cartService
    ) {
    }

    public function index(Request $request)
    {
        $designs = CustomDesign::query()
            ->where('user_id', $request->user()->id)
            ->with(['items.product'])
            ->withCount('orderDetails')
            ->latest('id')
            ->get();

        foreach ($designs as $design) {
            try {
                $recalculated = $this->designService->recalculate($design);

                $design->setAttribute(
                    'current_estimated_price',
                    $recalculated['pricing']['estimated_price']
                );
                $design->setAttribute('availability_error', null);
            } catch (\Throwable $exception) {
                $design->setAttribute('current_estimated_price', null);
                $design->setAttribute(
                    'availability_error',
                    $exception->getMessage()
                );
            }
        }

        return view('designs.index', compact('designs'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateDesign($request);

        $design = $this->designService->create(
            $request->user(),
            $validated['name'],
            $validated['style'],
            $validated['items']
        );

        return redirect()
            ->route('designs.index')
            ->with(
                'success',
                "Đã lưu thiết kế {$design->design_code}."
            );
    }

    public function edit(
        Request $request,
        CustomDesign $customDesign
    ) {
        $this->designService->assertOwnedAndEditable(
            $customDesign,
            $request->user()
        );

        return redirect()->route('studio.index', [
            'design' => $customDesign->id,
        ]);
    }

    public function update(
        Request $request,
        CustomDesign $customDesign
    ) {
        $validated = $this->validateDesign($request);

        $design = $this->designService->update(
            $customDesign,
            $request->user(),
            $validated['name'],
            $validated['style'],
            $validated['items']
        );

        return redirect()
            ->route('designs.index')
            ->with(
                'success',
                "Đã cập nhật thiết kế {$design->design_code}."
            );
    }

    public function destroy(
        Request $request,
        CustomDesign $customDesign
    ) {
        $this->designService->deleteForUser(
            $customDesign,
            $request->user()
        );

        return redirect()
            ->route('designs.index')
            ->with('success', 'Đã xóa thiết kế.');
    }

    public function addToCart(
        Request $request,
        CustomDesign $customDesign
    ) {
        if ($customDesign->user_id !== $request->user()->id) {
            abort(403);
        }

        $this->cartService->addCustomDesign(
            $request->user(),
            $customDesign
        );

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Đã thêm thiết kế vào giỏ hàng. Giá và tồn kho đã được kiểm tra lại.'
            );
    }

    private function validateDesign(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'style' => [
                'required',
                Rule::in([
                    'natural',
                    'forest',
                    'zen',
                    'fairy',
                    'tropical',
                    'minimal',
                ]),
            ],
            'items' => ['required', 'array', 'min:3', 'max:6'],
            'items.*.product_id' => [
                'required',
                'integer',
                'distinct',
                'exists:products,id',
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);
    }
}
