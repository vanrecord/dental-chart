<?php

namespace Modules\Hospital\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Hospital\Http\Requests\ProductRequest;
use Modules\Hospital\Models\Category;
use Modules\Hospital\Models\Service;
use Modules\User\Traits\AuthorizesWithPermissions;

class ProductController extends Controller
{
    use AuthorizesWithPermissions;

    public function index(Request $request): View
    {
        $this->checkPermission('hospital:products:view');

        $query = Service::where('type', Service::PRODUCT)->with('category');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $products = $query->orderBy(Service::raw('CAST(code AS INTEGER)'), 'asc')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::where('type', Category::PRODUCT)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('hospital::products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $this->checkPermission('hospital:products:create');

        $categories = Category::where('type', Category::PRODUCT)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('hospital::products.create', compact('categories'));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $this->checkPermission('hospital:products:create');

        $data = $request->validated();
        $data['type'] = Service::PRODUCT;

        Service::create($data);

        return redirect()->route('hospital.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(int $id): View
    {
        $this->checkPermission('hospital:products:view');

        $product = Service::where('type', Service::PRODUCT)
            ->with('category')
            ->findOrFail($id);

        return view('hospital::products.show', compact('product'));
    }

    public function edit(int $id): View
    {
        $this->checkPermission('hospital:products:edit');

        $product = Service::where('type', Service::PRODUCT)->findOrFail($id);

        $categories = Category::where('type', Category::PRODUCT)
            ->orderBy('name')
            ->get();

        return view('hospital::products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, int $id): RedirectResponse
    {
        $this->checkPermission('hospital:products:update');

        $product = Service::where('type', Service::PRODUCT)->findOrFail($id);

        $data = $request->validated();
        unset($data['code'], $data['type'], $data['hospital_id']);

        $product->update($data);

        return redirect()->route('hospital.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkPermission('hospital:products:delete');

        $product = Service::where('type', Service::PRODUCT)->findOrFail($id);
        $product->delete();

        return redirect()->route('hospital.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}

