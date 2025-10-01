<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }

    public function data()
    {
        $user = User::isNotAdmin()->orderBy('id', 'desc')->get();

        return datatables()
            ->of($user)
            ->addIndexColumn()
            ->addColumn('aksi', function ($user) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('user.update', $user->id) .'`)" class="btn btn-info btn-sm"><i class="cil-pencil" style="color:white;"></i></button>
                    <button type="button" onclick="deleteData(`'. route('user.destroy', $user->id) .'`)" class="btn btn-danger btn-sm"><i class="cil-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'name' => 'required|min:3',
                'username' => 'required|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'level' => 'required|in:1,2' // Sesuaikan dengan level sistem (1=admin, 2=kasir)
            ]);

            $user = new User();
            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            // Konversi level dari form ke angka
            $user->level = $request->level;
            $user->foto = '/img/user.jpg';
            
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error saving user: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function show($id)
    {
        $user = User::find($id);
        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 200);
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::find($id);
            
            // Validasi input
            $request->validate([
                'name' => 'required|min:3',
                'username' => 'required|unique:users,username,'.$id,
                'email' => 'required|email|unique:users,email,'.$id,
                'level' => 'required|in:1,2' // Sesuaikan dengan level sistem
            ]);

            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->level = $request->level;
            
            if ($request->has('password') && $request->password != "") {
                $request->validate([
                    'password' => 'required|min:6'
                ]);
                $user->password = bcrypt($request->password);
            }
            
            $user->update();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diupdate',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error updating user: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ], 200);
    }

    public function profil()
    {
        $profil = auth()->user();
        return view('user.profil', compact('profil'));
    }

    public function updateProfil(Request $request)
    {
        $user = auth()->user();
        
        // Validasi fleksibel: hanya name yang wajib, username/email opsional jika disubmit
        $request->validate([
            'name' => 'required|min:3',
            'username' => 'sometimes|nullable|unique:users,username,'.$user->id,
            'email' => 'sometimes|nullable|email|unique:users,email,'.$user->id,
        ]);
        
        $user->name = $request->name;
        if ($request->filled('username')) {
            $user->username = $request->username;
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password') && $request->password != "") {
            if (Hash::check($request->old_password, $user->password)) {
                if ($request->password == $request->password_confirmation) {
                    $user->password = bcrypt($request->password);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Konfirmasi password tidak sesuai'
                    ], 422);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Password lama tidak sesuai'
                ], 422);
            }
        }

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $nama = 'logo-' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/img'), $nama);

            $user->foto = "/img/$nama";
        }

        $user->update();

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui',
            'data' => $user
        ], 200);
    }
}
