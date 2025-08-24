@php
    use App\Enums\TypeUserEnum;
    use Illuminate\Support\Str;

    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'User Management | List')

<!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

<!-- Page Scripts -->
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

            // Handle modals
            const rideTypeModal = document.getElementById('rideTypeModal');

            // Handle delete confirmation
            document.querySelectorAll('.delete-record').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-id');

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
                            form.action = `${baseUrl}${authUserType}/users/${userId}`;
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

        function editUser(id) {
            // Fetch user data using AJAX
            $.ajax({
                url: `${baseUrl}${authUserType}/users/${id}/edit`,
                type: 'GET',
                dataType: 'json',
                success: function(user) {
                    $('#user_id').val(id);
                    $('#name').val(user.name);
                    $('#email').val(user.email);
                    $('#phone').val(user.phone);
                    $('#identity_id').val(user.identity_id);
                    $('#type').val(user.type);
                    if (user.status == 1) {
                        $('#status').prop('checked', true);
                    } else {
                        $('#status').prop('checked', false);
                    }
                    $('#userForm').attr('action', `${baseUrl}${authUserType}/users/${id}`);
                    if ($('#userForm input[name="_method"]').length === 0) {
                        $('#userForm').append('<input type="hidden" name="_method" value="PUT">');
                    }
                    $('#offcanvasUserLabel').text('Edit User');
                }
            });
        }

        $('#addUserButton').on('click', function() {
            $('#userForm')[0].reset();
            $('#userForm').find('input[name="_method"]').remove();
            $('#userForm').attr('action', `${baseUrl}${authUserType}/users`);
            $('#offcanvasUserLabel').text('Add User');
        });

        $('#offcanvasUser').on('hidden.bs.offcanvas', function() {
            $('#userForm')[0].reset();
            $('#userForm').find('input[name="_method"]').remove();
            $('#userForm').attr('action', `${baseUrl}${authUserType}/users`);
            $('#offcanvasUserLabel').text('Add User');
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



    <!-- User Offcanvas Form -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasUser" aria-labelledby="offcanvasUserLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasUserLabel" class="offcanvas-title">{{ __('Add User') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="userForm" method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <input type="hidden" name="id" id="user_id">
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('Name') }}</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">{{ __('Phone') }}</label>
                    <input type="text" class="form-control" id="phone" name="phone">
                </div>
                <div class="mb-3">
                    <label for="identity_id" class="form-label">{{ __('User ID') }}</label>
                    <input type="text" class="form-control" id="identity_id" name="identity_id">
                </div>
                <div class="mb-3">
                    <label for="type" class="form-label">{{ __('Type') }}</label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                        <option value="driver">Driver</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <small class="text-muted">{{ __('Leave empty to keep current password when editing') }}</small>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="status" name="status">
                    <label class="form-check-label" for="status">{{ __('Active') }}</label>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                <button type="reset" class="btn btn-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
            </form>
        </div>
    </div>

    <!-- Dashboard Stats Cards -->
    <div class="row g-6 mb-6">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">{{ __('Users') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $totalUser }}</h4>
                                <p class="text-success mb-0">(100%)</p>
                            </div>
                            <small class="mb-0">{{ __('Total Users') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ti ti-user ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">{{ __('Number of nationalities') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $verified }}</h4>
                                <p class="text-success mb-0">(+95%)</p>
                            </div>
                            <small class="mb-0">{{ __('Recent analytics') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ti ti-user-check ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">{{ __('Duplicate Users') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $userDuplicates }}</h4>
                                <p class="text-success mb-0">(0%)</p>
                            </div>
                            <small class="mb-0">{{ __('Recent analytics') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ti ti-users ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users List Table -->
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Search Filter') }}</h5>
            <div>
                <button type="button" id="addUserButton" class="btn btn-sm btn-primary" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasUser">
                    <i class="ti ti-plus me-1"></i>{{ __('Add New User') }}
                </button>
            </div>
        </div>

        <!-- Search Form -->
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">{{ __('Search') }}</label>
                    <input type="text" class="form-control" id="search" name="search"
                        placeholder="{{ __('Search by name, ID, phone...') }}" value="{{ $search }}">
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
                        @foreach (['id' => 'ID', 'name' => 'Name', 'email' => 'Email', 'phone' => 'Phone'] as $value => $label)
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

        <!-- Users Table -->
        <div class="table-responsive">
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th>{{ __('Id') }}</th>
                        <th>{{ __('NAME') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('USER ID') }}</th>
                        <th>{{ __('Nationality') }}</th>
                        <th>{{ __('email') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $index => $user)
                        <tr>
                            <td>{{ $users->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex justify-content-start align-items-center user-name">
                                    <div class="avatar-wrapper">
                                        <div class="avatar avatar-sm me-4">
                                            @php
                                                $name = $user->name ?? '?';
                                                $initials = '';
                                                $nameParts = explode(' ', $name);
                                                if (count($nameParts) > 1) {
                                                    $initials =
                                                        mb_substr($nameParts[0], 0, 1) .
                                                        mb_substr($nameParts[count($nameParts) - 1], 0, 1);
                                                } else {
                                                    $initials = mb_substr($name, 0, 2);
                                                }
                                                $initials = mb_strtoupper($initials);

                                                $stateColors = [
                                                    'success',
                                                    'danger',
                                                    'warning',
                                                    'info',
                                                    'dark',
                                                    'primary',
                                                    'secondary',
                                                ];
                                                $stateColor = $stateColors[$user->id % count($stateColors)];
                                            @endphp
                                            @if ($user->image)
                                                <img src="{{ asset($user->image) }}" alt="User profile picture"
                                                    class="rounded-circle">
                                            @elseif($user->profile_photo_path)
                                                <img src="{{ asset($user->profile_photo_path) }}"
                                                    alt="User profile picture" class="rounded-circle">
                                            @else
                                                <span
                                                    class="avatar-initial rounded-circle bg-label-{{ $stateColor }}">{{ $initials }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('admin.users.show', $user->id) }}"
                                            class="text-heading text-truncate">
                                            <span class="fw-medium">{{ $user->name }}</span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                            <td>{{ $user->type }}</td>
                            <td>{{ $user->identity_id }}</td>
                            <td>{{ $user->country->name_ar ?? 'N/A' }}</td>
                            <td>
                                @if ($user->email)
                                    <span class="badge bg-label-success">{{ $user->email }}</span>
                                @else
                                    <span class="badge bg-label-warning">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-50">
                                    <button
                                        class="btn btn-sm btn-icon edit-record btn-text-secondary rounded-pill waves-effect"
                                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasUser"
                                        onclick="editUser({{ $user->id }})">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button
                                        class="btn btn-sm btn-icon delete-record btn-text-secondary rounded-pill waves-effect"
                                        data-id="{{ $user->id }}">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                    <div class="dropdown ms-1">
                                        <button
                                            class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end m-0">
                                            <a href="{{ route('admin.users.show', $user->id) }}"
                                                class="dropdown-item">View</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if ($users->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center">{{ __('No users found') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer d-flex justify-content-center pt-3">
            {{ $users->appends(['search' => $search, 'per_page' => $perPage, 'sort' => $sort, 'order' => $order])->links() }}
        </div>
    </div>
@endsection
