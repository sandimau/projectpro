<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\CompanyProvisioner;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('company_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $companies = Company::query()->orderBy('name')->get();

        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        abort_if(Gate::denies('company_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.companies.create');
    }

    public function store(Request $request, CompanyProvisioner $provisioner)
    {
        abort_if(Gate::denies('company_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('companies', 'slug'),
            ],
            'is_active' => 'nullable|boolean',
            'admin_name' => 'nullable|string|max:100',
            'admin_email' => 'nullable|email|max:150',
            'admin_password' => 'nullable|string|min:6|max:100',
        ], [
            'slug.regex' => 'Slug hanya boleh huruf kecil, angka, dan strip (untuk subdomain).',
        ]);

        $result = $provisioner->create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'is_active' => $request->boolean('is_active', true),
            'admin_name' => $validated['admin_name'] ?? 'Admin',
            'admin_email' => $validated['admin_email'] ?? null,
            'admin_password' => $validated['admin_password'] ?? null,
        ]);

        $msg = 'Company dibuat: '.$result['company']->name.' ('.$result['company']->slug.')';
        if ($result['admin']) {
            $msg .= ' Admin: '.$result['admin']->email;
        }

        return redirect()->route('companies.index')->withSuccess($msg);
    }

    public function edit(Company $company)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        abort_if(Gate::denies('company_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('companies', 'slug')->ignore($company->id),
            ],
            'is_active' => 'nullable|boolean',
            'office_latitude' => 'nullable|numeric',
            'office_longitude' => 'nullable|numeric',
            'max_distance_radius' => 'nullable|integer|min:0',
            'clock_in_time' => 'nullable|string|max:10',
            'clock_out_time' => 'nullable|string|max:10',
            'late_tolerance_minutes' => 'nullable|integer|min:0',
            'qr_code_secret' => 'nullable|string|max:255',
            'fonnte_token' => 'nullable|string|max:255',
            'whatsapp_group_target' => 'nullable|string|max:255',
        ], [
            'slug.regex' => 'Slug hanya boleh huruf kecil, angka, dan strip (untuk subdomain).',
        ]);

        $settings = array_merge($company->settings ?? [], [
            'office_latitude' => $validated['office_latitude'] ?? data_get($company->settings, 'office_latitude'),
            'office_longitude' => $validated['office_longitude'] ?? data_get($company->settings, 'office_longitude'),
            'max_distance_radius' => $validated['max_distance_radius'] ?? data_get($company->settings, 'max_distance_radius'),
            'clock_in_time' => $validated['clock_in_time'] ?? data_get($company->settings, 'clock_in_time'),
            'clock_out_time' => $validated['clock_out_time'] ?? data_get($company->settings, 'clock_out_time'),
            'late_tolerance_minutes' => $validated['late_tolerance_minutes'] ?? data_get($company->settings, 'late_tolerance_minutes'),
            'qr_code_secret' => $validated['qr_code_secret'] ?? data_get($company->settings, 'qr_code_secret'),
            'fonnte_token' => $validated['fonnte_token'] ?? data_get($company->settings, 'fonnte_token'),
            'whatsapp_group_target' => $validated['whatsapp_group_target'] ?? data_get($company->settings, 'whatsapp_group_target'),
        ]);

        $company->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'is_active' => $request->boolean('is_active'),
            'settings' => $settings,
        ]);

        return redirect()->route('companies.index')->withSuccess(__('Company updated successfully.'));
    }
}
