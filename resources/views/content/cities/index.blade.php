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
    <!-- Include jQuery and SweetAlert -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function confirmDelete(cityId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm-' + cityId).submit();
                }
            });
        }

        function editCity(id, name_en, name_ar) {
            $('#city_id').val(id);
            $('#name_en').val(name_en);
            $('#name_ar').val(name_ar);
            $('#cityForm').attr('action', '/admin/cities/' + id);
            if ($('#cityForm input[name="_method"]').length === 0) {
                $('#cityForm').append('<input type="hidden" name="_method" value="PUT">');
            }
            $('#offcanvasCityLabel').text('Edit City');
        }

        $('#addCityButton').on('click', function() {
            $('#cityForm')[0].reset();
            $('#cityForm').find('input[name="_method"]').remove();
            $('#cityForm').attr('action', '{{ route('admin.cities.store') }}');
            $('#offcanvasCityLabel').text('Add City');
        });

        $('#offcanvasCity').on('hidden.bs.offcanvas', function() {
            $('#cityForm')[0].reset();
            $('#cityForm').find('input[name="_method"]').remove();
            $('#cityForm').attr('action', '{{ route('admin.cities.store') }}');
            $('#offcanvasCityLabel').text('Add City');
        });
    </script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Button to trigger offcanvas form -->
        <div class="row mb-3">
            <button id="addCityButton" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCity">
                {{ __('Add City') }}
            </button>
        </div>
        <!-- Cities Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table datatables-basic">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Name (EN)') }}</th>
                                    <th>{{ __('Name (AR)') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cities as $city)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $city->getTranslation('name', 'en') }}</td>
                                        <td>{{ $city->getTranslation('name', 'ar') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="offcanvas"
                                                data-bs-target="#offcanvasCity"
                                                onclick="editCity({{ $city->id }}, '{{ addslashes($city->getTranslation('name', 'en')) }}', '{{ addslashes($city->getTranslation('name', 'ar')) }}')">
                                                {{ __('Edit') }}
                                            </button>
                                            <form id="deleteForm-{{ $city->id }}"
                                                action="{{ route('admin.cities.destroy', $city->id) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete({{ $city->id }})">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Offcanvas Form for Add/Edit City -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCity" aria-labelledby="offcanvasCityLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasCityLabel" class="offcanvas-title">{{ __('Add City') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <form id="cityForm" method="POST" action="{{ route('admin.cities.store') }}">
                    @csrf
                    <input type="hidden" name="id" id="city_id">
                    <div class="mb-3">
                        <label for="name_en" class="form-label">{{ __('Name (EN)') }}</label>
                        <input type="text" class="form-control" id="name_en" name="name_en" required>
                    </div>
                    <div class="mb-3">
                        <label for="name_ar" class="form-label">{{ __('Name (AR)') }}</label>
                        <input type="text" class="form-control" id="name_ar" name="name_ar" required>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                    <button type="reset" class="btn btn-secondary"
                        data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
