@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Cities Management')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Country Details') }}</h5>
                    <a href="{{ route('admin.countries.index') }}" class="btn btn-secondary btn-sm">
                        <i class="ti ti-arrow-left me-1"></i>{{ __('Back to List') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    @if ($country->image)
                                        <img src="{{ asset('storage/' . $country->image) }}" alt="{{ $country->name_en }}"
                                            class="img-fluid mb-3" style="max-height: 150px;">
                                    @else
                                        <div class="mb-3 p-4 bg-label-secondary rounded">
                                            <i class="ti ti-photo-off ti-lg"></i>
                                            <p class="mt-2">{{ __('No Flag Image') }}</p>
                                        </div>
                                    @endif
                                    <h4>{{ $country->name_ar }}</h4>
                                    <h5 class="text-muted">{{ $country->name_en ?? 'N/A' }}</h5>
                                    <div class="d-flex justify-content-center mt-3">
                                        <span class="badge bg-label-primary fs-6 me-2">{{ $country->code }}</span>
                                        @if ($country->status)
                                            <span class="badge bg-label-success fs-6">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge bg-label-danger fs-6">{{ __('Inactive') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-8">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ __('Country Information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h6 class="fw-semibold">{{ __('Exchange Rate') }}</h6>
                                            <p>{{ $country->exchange_rate }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-semibold">{{ __('Tax Rate') }}</h6>
                                            <p>{{ $country->country_tax }}%</p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h6 class="fw-semibold">{{ __('Arabic Symbol') }}</h6>
                                            <p>{{ $country->symbol_ar ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-semibold">{{ __('English Symbol') }}</h6>
                                            <p>{{ $country->symbol_en ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h6 class="fw-semibold">{{ __('Latitude') }}</h6>
                                            <p>{{ $country->latitude ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-semibold">{{ __('Longitude') }}</h6>
                                            <p>{{ $country->longitude ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h6 class="fw-semibold">{{ __('Created At') }}</h6>
                                            <p>{{ $country->created_at->format('Y-m-d H:i:s') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-semibold">{{ __('Last Updated') }}</h6>
                                            <p>{{ $country->updated_at->format('Y-m-d H:i:s') }}</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="fw-semibold">{{ __('Users from this country') }}</h6>
                                            <p>{{ $userCount }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (count($cities) > 0)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('Cities in this Country') }}</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Name') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cities as $index => $city)
                                            <tr>
                                                <td>{{ $cities->firstItem() + $index }}</td>
                                                <td>{{ $city->name_en }}</td>
                                                <td>{{ $city->name_ar }}</td>
                                                {{-- <td>
                                                    @if ($city->status)
                                                        <span class="badge bg-label-success">{{ __('Active') }}</span>
                                                    @else
                                                        <span class="badge bg-label-danger">{{ __('Inactive') }}</span>
                                                    @endif
                                                </td> --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer d-flex justify-content-center">
                                {{ $cities->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
