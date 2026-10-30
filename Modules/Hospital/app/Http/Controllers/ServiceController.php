<?php

namespace Modules\Hospital\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Hospital\Http\Requests\ServiceRequest;
use Modules\Hospital\Models\Category;
use Modules\Hospital\Models\Service;
use Modules\User\Traits\AuthorizesWithPermissions;

class ServiceController extends Controller
{
    use AuthorizesWithPermissions;

    public function index(Request $request): View
    {
        $this->checkPermission('hospital:services:view');

        $query = Service::where('type', Service::SERVICE)->with('category');

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

        $services = $query->orderBy(Service::raw('CAST(code AS INTEGER)'), 'asc')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::where('type', Category::SERVICE)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('hospital::services.index', compact('services', 'categories'));
    }

    public function create(): View
    {
        $this->checkPermission('hospital:services:create');

        $categories = Category::where('type', Category::SERVICE)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('hospital::services.create', compact('categories'));
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $this->checkPermission('hospital:services:create');

        $data = $request->validated();
        $data['type'] = Service::SERVICE;

        Service::create($data);

        return redirect()->route('hospital.services.index')
            ->with('success', 'Service created successfully.');
    }

    public function show(int $id): View
    {
        $this->checkPermission('hospital:services:view');

        $service = Service::where('type', Service::SERVICE)
            ->with('category')
            ->findOrFail($id);

        return view('hospital::services.show', compact('service'));
    }

    public function edit(int $id): View
    {
        $this->checkPermission('hospital:services:edit');

        $service = Service::where('type', Service::SERVICE)->findOrFail($id);

        $categories = Category::where('type', Category::SERVICE)
            ->orderBy('name')
            ->get();

        return view('hospital::services.edit', compact('service', 'categories'));
    }

    public function update(ServiceRequest $request, int $id): RedirectResponse
    {
        $this->checkPermission('hospital:services:update');

        $service = Service::where('type', Service::SERVICE)->findOrFail($id);

        $data = $request->validated();
        unset($data['code'], $data['type'], $data['hospital_id']);

        $service->update($data);

        return redirect()->route('hospital.services.index')
            ->with('success', 'Service updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkPermission('hospital:services:delete');

        $service = Service::where('type', Service::SERVICE)->findOrFail($id);
        $service->delete();

        return redirect()->route('hospital.services.index')
            ->with('success', 'Service deleted successfully.');
    }
}

