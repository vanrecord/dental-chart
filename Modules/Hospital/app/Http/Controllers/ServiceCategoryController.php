<?php

namespace Modules\Hospital\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Hospital\Http\Requests\ServiceCategoryRequest;
use Modules\Hospital\Models\Category;
use Modules\User\Traits\AuthorizesWithPermissions;

class ServiceCategoryController extends Controller
{
    use AuthorizesWithPermissions;

    public function index(Request $request): View
    {
        $this->checkPermission('hospital:service-categories:view');

        $query = Category::where('type', Category::SERVICE)->withCount('services');

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

        return view('hospital::service-categories.index', compact('categories'));
    }

    public function create(): View
    {
        $this->checkPermission('hospital:service-categories:create');

        return view('hospital::service-categories.create');
    }

    public function store(ServiceCategoryRequest $request): RedirectResponse
    {
        $this->checkPermission('hospital:service-categories:create');

        $data = $request->validated();
        $data['type'] = Category::SERVICE;

        Category::create($data);

        return redirect()->route('hospital.service-categories.index')
            ->with('success', 'Service category created successfully.');
    }

    public function show(int $id): View
    {
        $this->checkPermission('hospital:service-categories:view');

        $category = Category::where('type', Category::SERVICE)
            ->with(['services' => function ($q) {
                $q->orderBy('code', 'asc');
            }])
            ->findOrFail($id);

        return view('hospital::service-categories.show', compact('category'));
    }

    public function edit(int $id): View
    {
        $this->checkPermission('hospital:service-categories:edit');

        $category = Category::where('type', Category::SERVICE)->findOrFail($id);

        return view('hospital::service-categories.edit', compact('category'));
    }

    public function update(ServiceCategoryRequest $request, int $id): RedirectResponse
    {
        $this->checkPermission('hospital:service-categories:update');

        $category = Category::where('type', Category::SERVICE)->findOrFail($id);

        $data = $request->validated();
        unset($data['code'], $data['type'], $data['hospital_id']);

        $category->update($data);

        return redirect()->route('hospital.service-categories.index')
            ->with('success', 'Service category updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkPermission('hospital:service-categories:delete');

        $category = Category::where('type', Category::SERVICE)->findOrFail($id);

        if ($category->services()->exists()) {
            return redirect()->route('hospital.service-categories.index')
                ->with('error', 'Cannot delete service category: active services are associated with it.');
        }

        $category->delete();

        return redirect()->route('hospital.service-categories.index')
            ->with('success', 'Service category deleted successfully.');
    }
}

