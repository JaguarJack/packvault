<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\BuildPackageJob;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PackageJobController extends Controller
{
    //
    public function index(Request $request)
    {
        $jobs = BuildPackageJob::query()
            ->with('package')
            ->orderByDesc('id')
            ->paginate($request->get('per_page', 10));

        return Inertia::render('job/Index', [
            'jobs' => $jobs
        ]);
    }

    public function destroy($id)
    {
        BuildPackageJob::destroy($id);

        return to_route('package.job')->with('success', '任务已被删除');
    }
}
