<?php

namespace Modules\Hospital\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Hospital\Http\Requests\ProductCategoryRequest;
use Modules\Hospital\Models\Category;
use Modules\User\Traits\AuthorizesWithPermissions;

class ProductCategoryController extends Controller
{
    use AuthorizesWithPermissions;

    public function index(Request $request): View
    {
        $this->checkPermission('hospital:product-categories:view');

        $query = Category::where('type', Category::PRODUCT)->withCount('services');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $categories = $query->orderBy(Category::raw('CAST(code AS INTEGER)'), 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('hospital::product-categories.index', compact('categories'));
    }

    public function create(): View
    {
        $this->checkPermission('hospital:product-categories:create');

        return view('hospital::product-categories.create');
    }

    public function store(ProductCategoryRequest $request): RedirectResponse
    {
        $this->checkPermission('hospital:product-categories:create');

        $data = $request->validated();
        $data['type'] = Category::PRODUCT;

        Category::create($data);

        return redirect()->route('hospital.product-categories.index')
            ->with('success', 'Product category created successfully.');
    }

    public function show(int $id): View
    {
        $this->checkPermission('hospital:product-categories:view');

        $category = Category::where('type', Category::PRODUCT)
            ->with(['services' => function ($q) {
                $q->orderBy('code', 'asc');
            }])
            ->findOrFail($id);

        return view('hospital::product-categories.show', compact('category'));
    }

    public function edit(int $id): View
    {
        $this->checkPermission('hospital:product-categories:edit');

        $category = Category::where('type', Category::PRODUCT)->findOrFail($id);

        return view('hospital::product-categories.edit', compact('category'));
    }

    public function update(ProductCategoryRequest $request, int $id): RedirectResponse
    {
        $this->checkPermission('hospital:product-categories:update');

        $category = Category::where('type', Category::PRODUCT)->findOrFail($id);

        $data = $request->validated();
        unset($data['code'], $data['type'], $data['hospital_id']);

        $category->update($data);

        return redirect()->route('hospital.product-categories.index')
            ->with('success', 'Product category updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkPermission('hospital:product-categories:delete');

        $category = Category::where('type', Category::PRODUCT)->findOrFail($id);

        if ($category->services()->exists()) {
            return redirect()->route('hospital.product-categories.index')
                ->with('error', 'Cannot delete product category: active products are associated with it.');
        }

        $category->delete();

        return redirect()->route('hospital.product-categories.index')
            ->with('success', 'Product category deleted successfully.');
    }
}

