<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SocialWeb;
use App\Models\Entity;
use Illuminate\Support\Facades\Storage;

class SocialWebController extends Controller
{
    public function index()
    {
        $socialWebs = SocialWeb::with('entity')
            ->forUser(auth()->user())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('social.index', compact('socialWebs'));
    }

    public function create()
    {
        $entities = Entity::forUser(auth()->user())->get();
        return view('social.add', compact('entities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|exists:entities,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'small_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published'
        ]);

        if (!auth()->user()->canAccessEntity((int) $request->entity_id)) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para gestionar esta entidad'], 403);
        }

        $data = $request->only(['entity_id', 'title', 'description', 'status']);

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            $bannerPath = $request->file('banner_image')->store('social/banners', 'public');
            $data['banner_image'] = $bannerPath;
        }

        // Handle small image upload
        if ($request->hasFile('small_image')) {
            $smallPath = $request->file('small_image')->store('social/images', 'public');
            $data['small_image'] = $smallPath;
        }

        SocialWeb::create($data);

        return response()->json(['success' => true, 'message' => 'Web Social creada exitosamente']);
    }

    public function edit($id)
    {
        $socialWeb = SocialWeb::with('entity')
            ->forUser(auth()->user())
            ->findOrFail($id);
        $entities = Entity::forUser(auth()->user())->get();
        return view('social.edit', compact('socialWeb', 'entities'));
    }

    public function update(Request $request, $id)
    {
        $socialWeb = SocialWeb::forUser(auth()->user())->findOrFail($id);

        $request->validate([
            'entity_id' => 'required|exists:entities,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'small_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published'
        ]);

        if (!auth()->user()->canAccessEntity((int) $request->entity_id)) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para gestionar esta entidad'], 403);
        }

        $data = $request->only(['entity_id', 'title', 'description', 'status']);

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            // Delete old banner if exists
            if ($socialWeb->banner_image) {
                Storage::disk('public')->delete($socialWeb->banner_image);
            }
            $bannerPath = $request->file('banner_image')->store('social/banners', 'public');
            $data['banner_image'] = $bannerPath;
        }

        // Handle small image upload
        if ($request->hasFile('small_image')) {
            // Delete old small image if exists
            if ($socialWeb->small_image) {
                Storage::disk('public')->delete($socialWeb->small_image);
            }
            $smallPath = $request->file('small_image')->store('social/images', 'public');
            $data['small_image'] = $smallPath;
        }

        $socialWeb->update($data);

        return response()->json(['success' => true, 'message' => 'Web Social actualizada exitosamente']);
    }

    public function destroy($id)
    {
        $socialWeb = SocialWeb::forUser(auth()->user())->findOrFail($id);
        
        // Delete images if they exist
        if ($socialWeb->banner_image) {
            Storage::disk('public')->delete($socialWeb->banner_image);
        }
        if ($socialWeb->small_image) {
            Storage::disk('public')->delete($socialWeb->small_image);
        }

        $socialWeb->delete();

        return response()->json(['success' => true, 'message' => 'Web Social eliminada exitosamente']);
    }

    public function changeStatus($id)
    {
        $socialWeb = SocialWeb::forUser(auth()->user())->findOrFail($id);
        $socialWeb->status = $socialWeb->status === 'published' ? 'draft' : 'published';
        $socialWeb->save();

        return response()->json(['success' => true, 'message' => 'Estado actualizado exitosamente']);
    }

    public function storeEntity(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|exists:entities,id'
        ]);

        if (!auth()->user()->canAccessEntity((int) $request->entity_id)) {
            abort(403, 'No tienes permisos para gestionar esta entidad.');
        }

        return redirect()->route('social.add-design', ['entity_id' => $request->entity_id]);
    }

    public function addDesign($entity_id)
    {
        $entity = Entity::forUser(auth()->user())->findOrFail($entity_id);
        return view('social.add_design', compact('entity'));
    }
}
