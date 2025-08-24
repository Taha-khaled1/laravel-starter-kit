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



@section('page-script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Define base URL and auth user type for AJAX calls
        const baseUrl = '{{ url('/') }}/';
        const authUserType = 'admin';

        // Minimal JavaScript for essential functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Setup CSRF token for AJAX requests
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Handle delete confirmation
            document.querySelectorAll('.delete-record').forEach(button => {
                button.addEventListener('click', function() {
                    const countryId = this.getAttribute('data-id');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!',
                        customClass: {
                            confirmButton: 'btn btn-primary me-3',
                            cancelButton: 'btn btn-label-secondary'
                        },
                        buttonsStyling: false
                    }).then(function(result) {
                        if (result.value) {
                            // Create form for delete submission
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action =
                                `${baseUrl}${authUserType}/countries/${countryId}`;
                            form.style.display = 'none';
                            const methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = 'DELETE';
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken;
                            form.appendChild(methodInput);
                            form.appendChild(csrfInput);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                document.querySelectorAll('.alert-dismissible').forEach(alert => {
                    alert.classList.add('fade');
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });

        function editCountry(id) {
            // Fetch country data using AJAX
            $.ajax({
                url: `${baseUrl}${authUserType}/countries/${id}/edit`,
                type: 'GET',
                dataType: 'json',
                success: function(country) {
                    $('#country_id').val(id);
                    $('#name_ar').val(country.name_ar);
                    $('#name_en').val(country.name_en);
                    $('#code').val(country.code);
                    $('#exchange_rate').val(country.exchange_rate);
                    $('#country_tax').val(country.country_tax);
                    $('#latitude').val(country.latitude);
                    $('#longitude').val(country.longitude);
                    $('#symbol_ar').val(country.symbol_ar);
                    $('#symbol_en').val(country.symbol_en);

                    if (country.status == 1) {
                        $('#status').prop('checked', true);
                    } else {
                        $('#status').prop('checked', false);
                    }

                    // Show current image if exists
                    if (country.image) {
                        $('#current_image').html(
                            `<img src="${baseUrl}storage/${country.image}" alt="Country Flag" class="img-thumbnail" style="max-height: 100px;">`
                        );
                        $('#current_image').show();
                    } else {
                        $('#current_image').hide();
                    }

                    $('#countryForm').attr('action', `${baseUrl}${authUserType}/countries/${id}`);
                    if ($('#countryForm input[name="_method"]').length === 0) {
                        $('#countryForm').append('<input type="hidden" name="_method" value="PUT">');
                    }
                    $('#offcanvasCountryLabel').text('Edit Country');
                }
            });
        }

        $('#addCountryButton').on('click', function() {
            $('#countryForm')[0].reset();
            $('#countryForm').find('input[name="_method"]').remove();
            $('#countryForm').attr('action', `${baseUrl}${authUserType}/countries`);
            $('#offcanvasCountryLabel').text('Add Country');
            $('#current_image').hide();
        });

        $('#offcanvasCountry').on('hidden.bs.offcanvas', function() {
            $('#countryForm')[0].reset();
            $('#countryForm').find('input[name="_method"]').remove();
            $('#countryForm').attr('action', `${baseUrl}${authUserType}/countries`);
            $('#offcanvasCountryLabel').text('Add Country');
            $('#current_image').hide();
        });
    </script>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible" role="alert" id="warningAlert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Country Offcanvas Form -->
    <!-- Country Offcanvas Form -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCountry" aria-labelledby="offcanvasCountryLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasCountryLabel" class="offcanvas-title">{{ __('Add Country') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="countryForm" method="POST" action="{{ route('admin.countries.store') }}"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="country_id">

                <div class="mb-3">
                    <label for="name_ar" class="form-label">{{ __('Arabic Name') }} *</label>
                    <input type="text" class="form-control" id="name_ar" name="name_ar" required>
                </div>

                <div class="mb-3">
                    <label for="name_en" class="form-label">{{ __('English Name') }}</label>
                    <input type="text" class="form-control" id="name_en" name="name_en">
                </div>

                <div class="mb-3">
                    <label for="code" class="form-label">{{ __('Country Code') }} *</label>
                    <input type="text" class="form-control" id="code" name="code" required>
                    <small class="text-muted">{{ __('ISO country code (e.g., SA, US, UK)') }}</small>
                </div>

                <div class="mb-3">
                    <label for="exchange_rate" class="form-label">{{ __('Exchange Rate') }} *</label>
                    <input type="number" step="0.000001" class="form-control" id="exchange_rate" name="exchange_rate"
                        required>
                </div>

                <div class="mb-3">
                    <label for="country_tax" class="form-label">{{ __('Country Tax (%)') }}</label>
                    <input type="number" step="0.1" min="0" max="100" class="form-control" id="country_tax"
                        name="country_tax">
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="symbol_ar" class="form-label">{{ __('Arabic Symbol') }}</label>
                        <input type="text" class="form-control" id="symbol_ar" name="symbol_ar">
                    </div>
                    <div class="col-md-6">
                        <label for="symbol_en" class="form-label">{{ __('English Symbol') }}</label>
                        <input type="text" class="form-control" id="symbol_en" name="symbol_en">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="latitude" class="form-label">{{ __('Latitude') }}</label>
                        <input type="number" step="any" class="form-control" id="latitude" name="latitude">
                    </div>
                    <div class="col-md-6">
                        <label for="longitude" class="form-label">{{ __('Longitude') }}</label>
                        <input type="number" step="any" class="form-control" id="longitude" name="longitude">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">{{ __('Country Flag') }}</label>
                    <input type="file" class="form-control" id="image" name="image">
                    <div id="current_image" class="mt-2" style="display: none;"></div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="status" name="status" checked>
                    <label class="form-check-label" for="status">{{ __('Active') }}</label>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                <button type="reset" class="btn btn-secondary"
                    data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
            </form>
        </div>
    </div>

    <!-- Dashboard Stats Cards -->
    <div class="row g-6 mb-6">
        <div class="col-sm-6 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">{{ __('Countries') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $totalCountries }}</h4>
                                <p class="text-success mb-0">(100%)</p>
                            </div>
                            <small class="mb-0">{{ __('Total Countries') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ti ti-world ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">{{ __('Active Countries') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $activeCountries }}</h4>
                                <p class="text-success mb-0">
                                    ({{ $totalCountries > 0 ? round(($activeCountries / $totalCountries) * 100) : 0 }}%)
                                </p>
                            </div>
                            <small class="mb-0">{{ __('Available for selection') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ti ti-check ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">{{ __('Inactive Countries') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $inactiveCountries }}</h4>
                                <p class="text-danger mb-0">
                                    ({{ $totalCountries > 0 ? round(($inactiveCountries / $totalCountries) * 100) : 0 }}%)
                                </p>
                            </div>
                            <small class="mb-0">{{ __('Hidden from selection') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ti ti-x ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Countries List Table -->
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Search Filter') }}</h5>
            <div>
                <button type="button" id="addCountryButton" class="btn btn-sm btn-primary" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasCountry">
                    <i class="ti ti-plus me-1"></i>{{ __('Add New Country') }}
                </button>
            </div>
        </div>

        <!-- Search Form -->
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('admin.countries.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">{{ __('Search') }}</label>
                    <input type="text" class="form-control" id="search" name="search"
                        placeholder="{{ __('Search by name, code...') }}" value="{{ $search }}">
                </div>
                <div class="col-md-2">
                    <label for="per_page" class="form-label">{{ __('Per Page') }}</label>
                    <select class="form-select" id="per_page" name="per_page">
                        @foreach ([10, 25, 50, 100] as $option)
                            <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort" class="form-label">{{ __('Sort By') }}</label>
                    <select class="form-select" id="sort" name="sort">
                        @foreach ([
            'id' => 'ID',
            'name_ar' => 'Arabic Name',
            'name_en' => 'English Name',
            'code' => 'Country Code',
            'exchange_rate' => 'Exchange Rate',
            'country_tax' => 'Tax Rate',
        ] as $value => $label)
                            <option value="{{ $value }}" {{ $sort == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="order" class="form-label">{{ __('Order') }}</label>
                    <select class="form-select" id="order" name="order">
                        <option value="asc" {{ $order == 'asc' ? 'selected' : '' }}>{{ __('Ascending') }}</option>
                        <option value="desc" {{ $order == 'desc' ? 'selected' : '' }}>{{ __('Descending') }}</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mb-0">{{ __('Filter') }}</button>
                </div>
            </form>
        </div>

        <!-- Countries Table -->
        <div class="table-responsive">
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Flag') }}</th>
                        <th>{{ __('Arabic Name') }}</th>
                        <th>{{ __('English Name') }}</th>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Exchange Rate') }}</th>
                        <th>{{ __('Tax Rate') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($countries as $index => $country)
                        <tr>
                            <td>{{ $countries->firstItem() + $index }}</td>
                            <td>
                                @if ($country->image)
                                    <img src="{{ asset('storage/' . $country->image) }}" alt="{{ $country->name_en }}"
                                        class="rounded" style="max-height: 40px; max-width: 60px;">
                                @else
                                    <span class="badge bg-label-secondary">{{ __('No Image') }}</span>
                                @endif
                            </td>
                            <td>{{ $country->name_ar }}</td>
                            <td>{{ $country->name_en ?? 'N/A' }}</td>
                            <td><span class="badge bg-label-info">{{ $country->code }}</span></td>
                            <td>{{ $country->exchange_rate }}</td>
                            <td>{{ $country->country_tax }}%</td>
                            <td>
                                @if ($country->status)
                                    <span class="badge bg-label-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-label-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-50">
                                    <button
                                        class="btn btn-sm btn-icon edit-record btn-text-secondary rounded-pill waves-effect"
                                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasCountry"
                                        onclick="editCountry({{ $country->id }})">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button
                                        class="btn btn-sm btn-icon delete-record btn-text-secondary rounded-pill waves-effect"
                                        data-id="{{ $country->id }}">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                    <div class="dropdown ms-1">
                                        <button
                                            class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end m-0">
                                            <a href="{{ route('admin.countries.show', $country->id) }}"
                                                class="dropdown-item">{{ __('View Details') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($countries->isEmpty())
                        <tr>
                            <td colspan="9" class="text-center">{{ __('No countries found') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer d-flex justify-content-center pt-3">
            {{ $countries->appends(['search' => $search, 'per_page' => $perPage, 'sort' => $sort, 'order' => $order])->links() }}
        </div>
    </div>
@endsection
