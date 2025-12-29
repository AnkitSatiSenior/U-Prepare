<?php
namespace App\Http\Controllers;

use App\Models\NavbarItem;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NavbarItemController extends Controller
{
    public function index()
    {
        try {
            $navbarItems = NavbarItem::with('children')->whereNull('parent_id')->orderBy('order')->get();
            return view('admin.navbar.index', compact('navbarItems'));
        } catch (\Exception $e) {
            Log::error('NavbarItemController@index - Error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Failed to load navbar items.',
                ]);
        }
    }

    public function create()
    {
        try {
            $parents = NavbarItem::where('is_dropdown', true)->get();
            return view('admin.navbar.create', compact('parents'));
        } catch (\Exception $e) {
            Log::error('NavbarItemController@create - Error: ' . $e->getMessage());
            return redirect()
                ->route('admin.navbar-items.index')
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Failed to load creation form.',
                ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->validateRequest($request);

            if ($validated['parent_id']) {
                $parent = NavbarItem::find($validated['parent_id']);
                if (!$parent || !$parent->is_dropdown) {
                    throw new \Exception('Selected parent must be a dropdown item');
                }
            }

            $validated = $this->processDefaults($validated, $request);
            $navbarItem = NavbarItem::create($validated);

            if (!$validated['is_dropdown']) {
                $this->createOrUpdatePage($navbarItem);
            }

            return redirect()
                ->route('admin.navbar-items.index')
                ->with('toast', [
                    'type' => 'success',
                    'title' => 'Success',
                    'message' => 'Navbar item created successfully!',
                ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = collect($e->errors())->flatten()->implode('<br>');
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Validation Error',
                    'message' => 'Please fix these errors:<br>' . $errorMessages,
                    'duration' => 8000,
                ]);
        } catch (\Exception $e) {
            Log::error('NavbarItemController@store - Error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Failed to create navbar item.',
                ]);
        }
    }

    public function show(NavbarItem $navbarItem)
    {
        try {
            return view('admin.navbar.show', compact('navbarItem'));
        } catch (\Exception $e) {
            Log::error('NavbarItemController@show - Error: ' . $e->getMessage());
            return redirect()
                ->route('admin.navbar-items.index')
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Failed to load navbar item details.',
                ]);
        }
    }

    public function edit(NavbarItem $navbarItem)
    {
        try {
            $parents = NavbarItem::where('is_dropdown', true)->where('id', '!=', $navbarItem->id)->get();
            return view('admin.navbar.edit', compact('navbarItem', 'parents'));
        } catch (\Exception $e) {
            Log::error('NavbarItemController@edit - Error: ' . $e->getMessage());
            return redirect()
                ->route('admin.navbar-items.index')
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Failed to load edit form.',
                ]);
        }
    }

    public function update(Request $request, NavbarItem $navbarItem)
    {
        try {
            $validated = $this->validateRequest($request, $navbarItem);

            if (!empty($validated['parent_id'])) {
                $parent = NavbarItem::find($validated['parent_id']);
                if (!$parent || !$parent->is_dropdown) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('toast', [
                            'type' => 'error',
                            'title' => 'Validation Error',
                            'message' => 'Selected parent must be a dropdown item.',
                            'data' => $request->all(),
                        ]);
                }
            }

            $validated = $this->processDefaults($validated, $request, $navbarItem);
            $navbarItem->update($validated);

            if (!$validated['is_dropdown']) {
                $this->createOrUpdatePage($navbarItem);
            }

            return redirect()
                ->route('admin.navbar-items.index')
                ->with('toast', [
                    'type' => 'success',
                    'title' => 'Success',
                    'message' => 'Navbar item updated successfully!',
                ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Validation Error',
                    'message' => 'Validation failed: ' . $e->validator->errors()->first(),
                    'data' => $request->all(),
                ]);
        } catch (\Exception $e) {
            Log::error('NavbarItemController@update - Error: ' . $e->getMessage(), ['request' => $request->all()]);
            return redirect()
                ->back()
                ->withInput()
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Failed to update navbar item: ' . $e->getMessage(),
                    'data' => $request->all(),
                ]);
        }
    }

    public function destroy(NavbarItem $navbarItem)
    {
        try {
            $navbarItem->delete();
            return redirect()
                ->route('admin.navbar-items.index')
                ->with('toast', [
                    'type' => 'success',
                    'title' => 'Success',
                    'message' => 'Navbar item deleted successfully!',
                ]);
        } catch (\Exception $e) {
            Log::error('NavbarItemController@destroy - Error: ' . $e->getMessage());
            return redirect()
                ->route('admin.navbar-items.index')
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'Failed to delete navbar item.',
                ]);
        }
    }

    public function updateOrder(Request $request)
    {
        try {
            foreach ($request->order as $order => $id) {
                NavbarItem::where('id', $id)->update(['order' => $order]);
            }

            return response()->json([
                'success' => true,
                'toast' => [
                    'type' => 'success',
                    'title' => 'Success',
                    'message' => 'Navigation order updated!',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('NavbarItemController@updateOrder - Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'toast' => [
                        'type' => 'error',
                        'title' => 'Error',
                        'message' => 'Failed to update order.',
                    ],
                ],
                500,
            );
        }
    }

    protected function validateRequest(Request $request, $navbarItem = null)
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:navbar_items' . ($navbarItem ? ',slug,' . $navbarItem->id : ''),
            'parent_id' => [
                'nullable',
                'exists:navbar_items,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $parent = NavbarItem::find($value);
                        if (!$parent || !$parent->is_dropdown) {
                            $fail('Selected parent must be a dropdown item');
                        }
                    }
                },
            ],
            'is_dropdown' => 'required|boolean',
            'order' => 'required|integer',
            'is_active' => 'required|boolean',
            'route' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
        ]);
    }

    protected function processDefaults(array $validated, Request $request, $navbarItem = null)
    {
        if (!$navbarItem || $request->title !== $navbarItem->title) {
            $validated['slug'] = $this->generateUniqueSlug($request->title);
        }

        $validated['is_dropdown'] = (bool) $validated['is_dropdown'];
        $validated['is_active'] = (bool) $validated['is_active'];
        $validated['order'] = $validated['order'] ?? ($navbarItem ? $navbarItem->order : 0);
        $validated['route'] = $validated['route'] ?? ($navbarItem ? $navbarItem->route : null);
        $validated['url'] = $validated['url'] ?? ($navbarItem ? $navbarItem->url : '#');

        return $validated;
    }

    protected function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (NavbarItem::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    protected function createOrUpdatePage(NavbarItem $navbarItem)
    {
        Page::updateOrCreate(
            ['slug' => $navbarItem->slug],
            [
                'title' => $navbarItem->title,
                'status' => $navbarItem->is_active,
                'meta_title' => $navbarItem->title,
                'meta_description' => '',
                'meta_keywords' => '',
                'body_eng' => '',
                'body_hindi' => '',
            ],
        );
    }
}
