@php
    use App\Enums\TypeUserEnum;
    use Illuminate\Support\Str;

    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Contact Us | List')

<!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Define base URL and auth user type for AJAX calls
        const baseUrl = '{{ url('/') }}/';
        const authUserType = 'admin';

        document.addEventListener('DOMContentLoaded', function() {
            // Setup CSRF token for AJAX requests
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Handle delete confirmation
            document.querySelectorAll('.delete-record').forEach(button => {
                button.addEventListener('click', function() {
                    const pageId = this.getAttribute('data-id');
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
                            form.action = `${baseUrl}${authUserType}/pages/${pageId}`;
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

        function editPage(id) {
            // Fetch page data using AJAX
            $.ajax({
                url: `${baseUrl}${authUserType}/pages/${id}/edit`,
                type: 'GET',
                dataType: 'json',
                success: function(page) {
                    $('#page_id').val(id);

                    // Fill in form fields with translatable content
                    $('#title_en').val(page.title_en || '');
                    $('#title_ar').val(page.title_ar || '');
                    $('#description_en').val(page.description_en || '');
                    $('#description_ar').val(page.description_ar || '');
                    $('#type').val(page.type);
                    $('#seo_title').val(page.seo_title || '');
                    $('#seo_description').val(page.seo_description || '');

                    if (page.status) {
                        $('#status').prop('checked', true);
                    } else {
                        $('#status').prop('checked', false);
                    }

                    // Update form action and method
                    $('#pageForm').attr('action', `${baseUrl}${authUserType}/pages/${id}`);
                    if ($('#pageForm input[name="_method"]').length === 0) {
                        $('#pageForm').append('<input type="hidden" name="_method" value="PUT">');
                    }

                    $('#offcanvasPageLabel').text('Edit Page');
                }
            });
        }

        $('#addPageButton').on('click', function() {
            $('#pageForm')[0].reset();
            $('#pageForm').find('input[name="_method"]').remove();
            $('#pageForm').attr('action', `${baseUrl}${authUserType}/pages`);
            $('#offcanvasPageLabel').text('Add Page');
        });

        $('#offcanvasPage').on('hidden.bs.offcanvas', function() {
            $('#pageForm')[0].reset();
            $('#pageForm').find('input[name="_method"]').remove();
            $('#pageForm').attr('action', `${baseUrl}${authUserType}/pages`);
            $('#offcanvasPageLabel').text('Add Page');
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

    <!-- Page Offcanvas Form -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasPage" aria-labelledby="offcanvasPageLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasPageLabel" class="offcanvas-title">{{ __('Add Page') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="pageForm" method="POST" action="{{ route('admin.pages.store') }}">
                @csrf
                <input type="hidden" name="id" id="page_id">

                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#navs-english"
                            aria-controls="navs-english" aria-selected="true">English</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#navs-arabic"
                            aria-controls="navs-arabic" aria-selected="false">Arabic</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#navs-settings"
                            aria-controls="navs-settings" aria-selected="false">Settings</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- English Tab -->
                    <div class="tab-pane fade show active" id="navs-english" role="tabpanel">
                        <div class="mb-3">
                            <label for="title_en" class="form-label">{{ __('Title (English)') }}</label>
                            <input type="text" class="form-control" id="title_en" name="title_en" required>
                        </div>
                        <div class="mb-3">
                            <label for="description_en" class="form-label">{{ __('Description (English)') }}</label>
                            <textarea class="form-control" id="description_en" name="description_en" rows="5" required></textarea>
                        </div>
                    </div>

                    <!-- Arabic Tab -->
                    <div class="tab-pane fade" id="navs-arabic" role="tabpanel">
                        <div class="mb-3">
                            <label for="title_ar" class="form-label">{{ __('Title (Arabic)') }}</label>
                            <input type="text" class="form-control" id="title_ar" name="title_ar" required>
                        </div>
                        <div class="mb-3">
                            <label for="description_ar" class="form-label">{{ __('Description (Arabic)') }}</label>
                            <textarea class="form-control" id="description_ar" name="description_ar" rows="5" required></textarea>
                        </div>
                    </div>

                    <!-- Settings Tab -->
                    <div class="tab-pane fade" id="navs-settings" role="tabpanel">
                        <div class="mb-3">
                            <label for="type" class="form-label">{{ __('Type') }}</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="about">About</option>
                                <option value="terms">Terms & Conditions</option>
                                <option value="privacy">Privacy Policy</option>
                                <option value="contact">Contact</option>
                                <option value="faq">FAQ</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="status" name="status" checked>
                            <label class="form-check-label" for="status">{{ __('Active') }}</label>
                        </div>
                        <div class="mb-3">
                            <label for="seo_title" class="form-label">{{ __('SEO Title') }}</label>
                            <input type="text" class="form-control" id="seo_title" name="seo_title">
                        </div>
                        <div class="mb-3">
                            <label for="seo_description" class="form-label">{{ __('SEO Description') }}</label>
                            <textarea class="form-control" id="seo_description" name="seo_description" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                    <button type="reset" class="btn btn-secondary"
                        data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
                </div>
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
                            <span class="text-heading">{{ __('Total Pages') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $totalPages }}</h4>
                            </div>
                            <small class="mb-0">{{ __('All pages') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ti ti-file-text ti-26px"></i>
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
                            <span class="text-heading">{{ __('Active Pages') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $activePages }}</h4>
                                <p class="text-success mb-0">
                                    ({{ $totalPages > 0 ? round(($activePages / $totalPages) * 100) : 0 }}%)</p>
                            </div>
                            <small class="mb-0">{{ __('Published content') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ti ti-file-check ti-26px"></i>
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
                            <span class="text-heading">{{ __('Page Types') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $pageTypes }}</h4>
                            </div>
                            <small class="mb-0">{{ __('Different page categories') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ti ti-category ti-26px"></i>
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
                            <span class="text-heading">{{ __('Inactive Pages') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $inactivePages }}</h4>
                                <p class="text-danger mb-0">
                                    ({{ $totalPages > 0 ? round(($inactivePages / $totalPages) * 100) : 0 }}%)</p>
                            </div>
                            <small class="mb-0">{{ __('Unpublished content') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ti ti-file-off ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pages List Table -->
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Search Filter') }}</h5>
            <div>
                <button type="button" id="addPageButton" class="btn btn-sm btn-primary" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasPage">
                    <i class="ti ti-plus me-1"></i>{{ __('Add New Page') }}
                </button>
            </div>
        </div>

        <!-- Search Form -->
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('admin.pages.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">{{ __('Search') }}</label>
                    <input type="text" class="form-control" id="search" name="search"
                        placeholder="{{ __('Search by title...') }}" value="{{ $search }}">
                </div>
                <div class="col-md-2">
                    <label for="type_filter" class="form-label">{{ __('Type') }}</label>
                    <select class="form-select" id="type_filter" name="type">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="about" {{ $type == 'about' ? 'selected' : '' }}>About</option>
                        <option value="terms" {{ $type == 'terms' ? 'selected' : '' }}>Terms & Conditions</option>
                        <option value="privacy" {{ $type == 'privacy' ? 'selected' : '' }}>Privacy Policy</option>
                        <option value="contact" {{ $type == 'contact' ? 'selected' : '' }}>Contact</option>
                        <option value="faq" {{ $type == 'faq' ? 'selected' : '' }}>FAQ</option>
                        <option value="custom" {{ $type == 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
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
                <div class="col-md-2">
                    <label for="sort" class="form-label">{{ __('Sort By') }}</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="id" {{ $sort == 'id' ? 'selected' : '' }}>ID</option>
                        <option value="type" {{ $sort == 'type' ? 'selected' : '' }}>Type</option>
                        <option value="created_at" {{ $sort == 'created_at' ? 'selected' : '' }}>Date Created</option>
                        <option value="updated_at" {{ $sort == 'updated_at' ? 'selected' : '' }}>Date Updated</option>
                    </select>
                </div>
                <div class="col-md-1">
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

        <!-- Pages Table -->
        <div class="table-responsive">
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Last Updated') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pages as $index => $page)
                        <tr>
                            <td>{{ $pages->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="avatar-wrapper">
                                        <div class="avatar avatar-sm me-3">
                                            @php
                                                $title = $page->getTranslation('title', 'en');
                                                $initials = strtoupper(substr($title, 0, 2));
                                                $stateColors = [
                                                    'success',
                                                    'danger',
                                                    'warning',
                                                    'info',
                                                    'dark',
                                                    'primary',
                                                    'secondary',
                                                ];
                                                $stateColor = $stateColors[$page->id % count($stateColors)];
                                            @endphp
                                            <span
                                                class="avatar-initial rounded-circle bg-label-{{ $stateColor }}">{{ $initials }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('admin.pages.show', $page->id) }}"
                                            class="text-heading text-truncate">
                                            <span class="fw-medium">{{ $page->getTranslation('title', 'en') }}</span>
                                        </a>
                                        <small>{{ Str::limit($page->getTranslation('title', 'ar'), 30) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-primary">{{ ucfirst($page->type) }}</span>
                            </td>
                            <td>
                                @if ($page->status)
                                    <span class="badge bg-label-success">Active</span>
                                @else
                                    <span class="badge bg-label-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $page->updated_at?->diffForHumans() }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-50">
                                    <button
                                        class="btn btn-sm btn-icon edit-record btn-text-secondary rounded-pill waves-effect"
                                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasPage"
                                        onclick="editPage({{ $page->id }})">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button
                                        class="btn btn-sm btn-icon delete-record btn-text-secondary rounded-pill waves-effect"
                                        data-id="{{ $page->id }}">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                    <div class="dropdown ms-1">
                                        <button
                                            class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end m-0">
                                            <a href="{{ route('admin.pages.show', $page->id) }}"
                                                class="dropdown-item">View</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($pages->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center">{{ __('No pages found') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer d-flex justify-content-center pt-3">
            {{ $pages->appends(['search' => $search, 'per_page' => $perPage, 'sort' => $sort, 'order' => $order, 'type' => $type])->links() }}
        </div>
    </div>
@endsection

@section('page-script')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Define base URL and auth user type for AJAX calls
        const baseUrl = '{{ url('/') }}/';
        const authUserType = 'admin';

        document.addEventListener('DOMContentLoaded', function() {
            // Setup CSRF token for AJAX requests
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Handle delete confirmation
            document.querySelectorAll('.delete-record').forEach(button => {
                button.addEventListener('click', function() {
                    const pageId = this.getAttribute('data-id');
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
                            form.action = `${baseUrl}${authUserType}/pages/${pageId}`;
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

        function editPage(id) {
            // Fetch page data using AJAX
            $.ajax({
                url: `${baseUrl}${authUserType}/pages/${id}/edit`,
                type: 'GET',
                dataType: 'json',
                success: function(page) {
                    $('#page_id').val(id);

                    // Fill in form fields with translatable content
                    $('#title_en').val(page.title_en || '');
                    $('#title_ar').val(page.title_ar || '');
                    $('#description_en').val(page.description_en || '');
                    $('#description_ar').val(page.description_ar || '');
                    $('#type').val(page.type);
                    $('#seo_title').val(page.seo_title || '');
                    $('#seo_description').val(page.seo_description || '');

                    if (page.status) {
                        $('#status').prop('checked', true);
                    } else {
                        $('#status').prop('checked', false);
                    }

                    // Update form action and method
                    $('#pageForm').attr('action', `${baseUrl}${authUserType}/pages/${id}`);
                    if ($('#pageForm input[name="_method"]').length === 0) {
                        $('#pageForm').append('<input type="hidden" name="_method" value="PUT">');
                    }

                    $('#offcanvasPageLabel').text('Edit Page');
                }
            });
        }

        $('#addPageButton').on('click', function() {
            $('#pageForm')[0].reset();
            $('#pageForm').find('input[name="_method"]').remove();
            $('#pageForm').attr('action', `${baseUrl}${authUserType}/pages`);
            $('#offcanvasPageLabel').text('Add Page');
        });

        $('#offcanvasPage').on('hidden.bs.offcanvas', function() {
            $('#pageForm')[0].reset();
            $('#pageForm').find('input[name="_method"]').remove();
            $('#pageForm').attr('action', `${baseUrl}${authUserType}/pages`);
            $('#offcanvasPageLabel').text('Add Page');
        });
    </script>
@endsection
