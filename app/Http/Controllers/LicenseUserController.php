<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\License;
use App\Models\LicenseUser;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class LicenseUserController extends Controller
{
    //
    public function index(Request $request)
    {
        return Inertia::render('licenseuser/Index', [
            'users' => LicenseUser::query()
                ->orderByDesc('id')
                ->paginate($request->get('per_page', 10))
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('licenseuser/Create', [
            'licenses' => License::all(['id', 'title'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'license_id' => 'required',
            'email' => ['required', 'email',
                    Rule::unique('license_users', 'email')->where('deleted_at')
                ],
            'allow_ip_number' => 'required|numeric',
        ], [
            'license_id.required' => 'License ID 必须填写',
            'email.required' => '用户邮箱必须填写',
            'email.email' => '用户邮箱格式不正确',
            'email.unique' => '用户邮箱已存在',
            'allow_ip_number.required' => '最大允许 IP 地址数',
        ]);

        $licenseUser = new LicenseUser();
        $licenseUser->license_id = $request->get('license_id');
        $licenseUser->email = $request->get('email');
        $licenseUser->allow_ip_number = $request->get('allow_ip_number');
        $licenseUser->license = md5($licenseUser->email .
                                           License::query()->where('id', $licenseUser->license_id)->value('license') .
                                           Str::random());
        $licenseUser->save();

        return to_route('license.user.index')->with('success', '新增用户成功');
    }


    public function edit($id)
    {
        return Inertia::render('licenseuser/Update', [
            'user' => LicenseUser::query()->findOrFail($id),
            'licenses' => License::all(['id', 'title'])
        ]);
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'license_id' => 'required',
            'email' => ['required', 'email', Rule::unique('license_users', 'email')->ignore($id)],
            'allow_ip_number' => 'required|numeric',
        ], [
            'license_id.required' => 'License ID 必须填写',
            'email.required' => '用户邮箱必须填写',
            'email.email' => '用户邮箱格式不正确',
            'email.unique' => '用户邮箱已存在',
            'allow_ip_number.required' => '最大允许 IP 地址数',
        ]);

        $licenseUser  = LicenseUser::query()->findOrFail($id);
        $licenseUser->license_id = $request->get('license_id');
        $licenseUser->email = $request->get('email');
        $licenseUser->allow_ip_number = $request->get('allow_ip_number');
        $licenseUser->save();

        return to_route('license.user.index')->with('success', '更新成功');
    }

    public function destroy($id)
    {
        LicenseUser::query()->where('id', $id)->delete();

        return to_route('license.user.index')->with('success', '用户删除成功');
    }

    public function activate($id)
    {
        $oldStatus = LicenseUser::query()->where('id', $id)->value('status');
        $status = $oldStatus == Status::DISABLE->value ? Status::ENABLE->value : Status::DISABLE->value;
        LicenseUser::query()->where('id', $id)
            ->update([
                'status' => $status
            ]);

        return to_route('license.user.index')->with('success', sprintf('用户已%s', $status?'启用':'禁用'));
    }
}
