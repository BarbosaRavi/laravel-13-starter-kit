<?php

namespace App\Services\Admin;

use App\Enums\UserTypeEnum;
use App\Exceptions\ApiException;
use App\Http\Resources\Admin\AdminResource;
use App\Models\Admin;
use App\Http\Resources\Admin\AdminCollection;
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
        $trashed = $data['trashed'] ?? null; 

        $query = Admin::query()
            ->when($trashed,
                fn ($query) => $query
                    ->whereHas('user', fn ($query) => $query->withTrashed())
                    ->with(['user' => fn ($query) => $query->withTrashed()]),
                fn ($query) => $query
                    ->whereHas('user')
                    ->with('user'))
            ->when($search, function ($query) use ($search, $trashed): void {
                $query->whereHas('user', function ($query) use ($search, $trashed): void {
                    if ($trashed) {
                        $query->withTrashed();
                    }

                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%");
                    });
                });
            })
            ->orderBy('created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        return new AdminCollection($query);
    }

    public function show(array $data): AdminResource
    {
        $admin = Admin::findOrFail($data['id']);
        return new AdminResource($admin->load('user'));
    }

    public function store(array $data): AdminResource
    {
        return DB::transaction(function () use ($data): AdminResource {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'user_type' => UserTypeEnum::SYS_ADMIN,
                'password' => Hash::make($data['password']),
            ]);

            $admin = Admin::create(['user_id' => $user->id]);
            $user->assignRole(UserTypeEnum::SYS_ADMIN->value)->save();

            return new AdminResource($admin->load('user'));
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

            if ($admin->user->isDirty('email')) {
                $exists = User::query()
                    ->withTrashed()
                    ->where('email', $data['email'])
                    ->exists();

                if ($exists) {
                    throw new ApiException('Email já está em uso', 422);
                }
            }

            $admin->user->save();

            return new AdminResource($admin->load('user'));
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
            $admin = Admin::findOrFail($data['id']);
            $admin->user()->withTrashed()->firstOrFail()->restore();
            return new AdminResource($admin->load('user'));
        });
    }


    public function destroy(array $data): void 
    {
        DB::transaction(function () use ($data): void {
            $admin = Admin::findOrFail($data['id']);
            $admin->user()->withTrashed()->firstOrFail()->forceDelete();
        });
    }
}