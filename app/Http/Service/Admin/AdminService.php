<?php

namespace App\Services\Admin;

use App\Exceptions\ApiException;
use App\Http\Resources\Admin\AdminResource;
use App\Models\Admin;
use App\Http\Resources\AdminCollection;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function index(array $data): AdminCollection
    {
        $page = $data['page'] ?? 1;
        $perPage = $data['per_page'] ?? 10;
        $search = $data['search'] ?? null;

        $query = Admin::query()
            ->with('user')
            ->when($search, function ($query, string $search): void {
                $query->whereHas('user', function ($query) use ($search): void {
                    $query
                        ->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%");
                });
            })
            ->orderBy('created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        return new AdminCollection($query);
    }

    public function store(array $data): AdminResource
    {
        return DB::transaction(function () use ($data): AdminResource {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $admin = Admin::create(['user_id' => $user->id]);

            return new AdminResource($admin);
        });
    }

    public function update(array $data): AdminResource
    {
        $admin = Admin::findOrFail($data['id']);

        return DB::transaction(function () use ($data, $admin): AdminResource {
            $updateData = [ 
                'name' => $data['name'],
                'email' => $data['email'],
            ];    
        
            $admin->user->fill($updateData);

            if ($admin->isDirty('email')) {
                $exists = User::query()
                    ->where('email', $data['email'])
                    ->exists();

                if ($exists) {
                    throw new ApiException('Email já está em uso');
                }
            }

            $admin->user->save();

            return new AdminResource($admin);
        });
    }

    public function delete(array $data): void 
    {
        DB::transaction(function () use ($data): void {
            Admin::findOrFail($data['id'])->user->delete();
        });
    }

    public function restore(array $data): AdminResource
    {
        return DB::transaction(function () use ($data): AdminResource {
            $admin = Admin::findOrFail($data['id'])->user->restore();
            return new AdminResource($admin);
        });
    }


    public function destroy(array $data): void 
    {
        DB::transaction(function () use ($data): void {
            Admin::findOrFail($data['id'])->user->forceDelete();
        });
    }
}