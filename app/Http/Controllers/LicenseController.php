<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Enums\SubscribeType;
use App\Models\License;
use App\Models\LicensePackage;
use App\Models\Team;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class LicenseController extends Controller
{
    //
    public function index(Request $request)
    {
        return Inertia::render('license/Index', [
            'licenses' => License::query()
                            ->with('packages')
                            ->orderByDesc('id')
                            ->paginate($request->get('per_page', 10))
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('license/Create', [
            'packages' => Package::getCurrenUserAvailablePackages($this->userId(), ['id as value', 'name as label']),
            'types' => License::types()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type' => sprintf('required|in:%s,%s', SubscribeType::YEAR->value, SubscribeType::FOREVER->value),
            'packages' => 'required',
        ], [
            'title.required' => 'license 名称必须填写',
            'type.required' => '请选择订阅方式',
            'type.in' => '目前只支持年付订阅和永久订阅',
            'packages.required' => '请选择授权 package'
        ]);

        $type = $request->get('type');

        if (SubscribeType::YEAR->value == $type) {
            $expiredAt = Carbon::now()->addYear()->timestamp;
        } else {
            $expiredAt = Carbon::now()->addYears(100)->timestamp;
        }

        $license  = new License();
        $license->title = $request->get('title');
        $license->user_id = $this->userId();
        $license->type = $type;
        $license->expired_at = $expiredAt;
        $license->license = md5(Str::of(Str::orderedUuid()->toString())->replace('-','')->toString());
        $license->save();

        $license->packages()->attach($request->get('packages'));

        return to_route('license.index');
    }


    public function edit($id)
    {
        $license = License::query()->findOrFail($id);

        $license['packages'] = LicensePackage::query()->where('license_id', $license->id)->pluck('package_id');

        return Inertia::render('license/Update', [
            'license' => $license,
            'packages' => Package::getCurrenUserAvailablePackages($this->userId(), ['id as value', 'name as label']),
            'types' => License::types()
        ]);
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type' => sprintf('required|in:%s,%s', SubscribeType::YEAR->value, SubscribeType::FOREVER->value),
            'packages' => 'required',
        ], [
            'title.required' => 'license 名称必须填写',
            'type.required' => '请选择订阅方式',
            'type.in' => '目前只支持年付订阅和永久订阅',
            'packages.required' => '请选择授权 package'
        ]);

        $type = $request->get('type');
        if (SubscribeType::YEAR->value == $type) {
            $expiredAt = Carbon::now()->addYear()->timestamp;
        } else {
            $expiredAt = Carbon::now()->addYears(100)->timestamp;
        }

        $license  = License::query()->findOrFail($id);
        $license->title = $request->get('title');
        $license->type = $type;
        $license->expired_at = $expiredAt;
        $license->save();

        $license->packages()->sync($request->get('packages'));

        return to_route('license.index');
    }

    public function destroy($id)
    {
        License::query()->where('id', $id)->delete();

        return to_route('license.index');
    }

    public function activate($id)
    {
        License::query()->where('id', $id)
            ->update([
                'status' => License::query()->where('id', $id)->value('status') == Status::DISABLE->value ? Status::ENABLE->value : Status::DISABLE->value
            ]);

        return to_route('license.index');
    }
}
