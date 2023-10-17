<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
class ProyekController extends Controller
{
    //
    public function index()
    {
        $data_proyek = Proyek::all();
        return view('admin.index', ['data_proyek' => $data_proyek]);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        if ($request->hasFile('image')) {
            $destination_path = '/uploads/image_proyek/';
            $image = $request->file('image');
            $filename = $image->getClientOriginalName();
            $filename_rand = time() . '_' . rand(1000, 9999) . $filename;

            // Directly move to the public path
            $image->move(public_path($destination_path), $filename_rand);

            $data['image'] = $filename_rand;
        }

        $proyek = Proyek::create($data);
        if (!$proyek) {
            return response()->json(['msg' => 'Gagal simpan dengan Ajax', 'status' => false]);
        } else {
            return response()->json(['msg' => 'Sukses simpan dengan Ajax', 'status' => true]);
        }
    }

    public function destroy($id)
    {
        $proyek = Proyek::find($id);

        // Code to delete the image or whatever is required...
        // Check if there's an image and if the image actually exists in the directory
        $imagePath = public_path('/uploads/image_proyek/' . $proyek->image);
        if ($proyek->image && file_exists($imagePath)) {
            unlink($imagePath);
        }

        $proyek->delete();

        return redirect('/admin/Proyek')->with('sukses', 'Data berhasil di-delete.');
    }

    public function edit($id)
    {
        $proyek = Proyek::find($id);
        return view('admin.edit', ['proyek' => $proyek]);
    }

    public function update(Request $request, $id)
    {
        $proyek = Proyek::find($id);
        $proyek->update($request->all());

        if ($request->hasFile('image')) {
            // Delete the existing image if it exists
            $existingImagePath = public_path('/uploads/image_proyek/' . $proyek->image);
            if ($proyek->image && file_exists($existingImagePath)) {
                unlink($existingImagePath);
            }

            // Upload new image and save the filename to the proyek
            $destination_path = '/uploads/image_proyek/';
            $image = $request->file('image');
            $filename = $image->getClientOriginalName();
            $filename_rand = time() . '_' . rand(1000, 9999) . $filename;

            // Directly move to the public path
            $image->move(public_path($destination_path), $filename_rand);

            $proyek->image = $filename_rand;
            $proyek->save();
        }

        return redirect('/admin')->with('sukses', 'Data berhasil di-update.');
    }
}
