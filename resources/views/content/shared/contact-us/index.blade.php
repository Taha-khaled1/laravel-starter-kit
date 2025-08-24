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

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                document.querySelectorAll('.alert-dismissible').forEach(alert => {
                    alert.classList.add('fade');
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });

        function viewEditMessage(id) {
            // Fetch message data using AJAX
            $.ajax({
                url: `${baseUrl}${authUserType}/contact-messages/${id}/edit`,
                type: 'GET',
                dataType: 'json',
                success: function(message) {
                    // Fill in form fields
                    $('#name').val(message.name);
                    $('#email').val(message.email);
                    $('#phone').val(message.phone);
                    $('#subject').val(message.subject);
                    $('#message').val(message.message);

                    // Update form action
                    $('#messageForm').attr('action', `${baseUrl}${authUserType}/contact-messages/${id}`);

                    $('#offcanvasMessageLabel').text('View/Edit Message');
                }
            });
        }
    </script>
@endsection



@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Message Offcanvas Form -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasMessage" aria-labelledby="offcanvasMessageLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasMessageLabel" class="offcanvas-title">{{ __('View/Edit Message') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="messageForm" method="POST" action="">
                @csrf
                @method('PUT')

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
                    <label for="subject" class="form-label">{{ __('Subject') }}</label>
                    <input type="text" class="form-control" id="subject" name="subject">
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">{{ __('Message') }}</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="offcanvas">{{ __('Close') }}</button>
                </div>
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
                            <span class="text-heading">{{ __('Total Messages') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $totalMessages }}</h4>
                            </div>
                            <small class="mb-0">{{ __('All contact requests') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ti ti-message ti-26px"></i>
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
                            <span class="text-heading">{{ __('Today') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $todayMessages }}</h4>
                            </div>
                            <small class="mb-0">{{ __('Messages received today') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ti ti-calendar-day ti-26px"></i>
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
                            <span class="text-heading">{{ __('Last 7 Days') }}</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $lastWeekMessages }}</h4>
                            </div>
                            <small class="mb-0">{{ __('Messages from last week') }}</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ti ti-calendar-week ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages List Table -->
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Search Filter') }}</h5>
        </div>

        <!-- Search Form -->
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('admin.contact-us.index') }}" class="row g-3">
                <div class="col-md-5">
                    <label for="search" class="form-label">{{ __('Search') }}</label>
                    <input type="text" class="form-control" id="search" name="search"
                        placeholder="{{ __('Search by name, email, subject...') }}" value="{{ $search }}">
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
                        <option value="created_at" {{ $sort == 'created_at' ? 'selected' : '' }}>Date</option>
                        <option value="name" {{ $sort == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="email" {{ $sort == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="subject" {{ $sort == 'subject' ? 'selected' : '' }}>Subject</option>
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

        <!-- Messages Table -->
        <div class="table-responsive">
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email / Phone') }}</th>
                        <th>{{ __('Subject') }}</th>
                        <th>{{ __('Message') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($messages as $index => $message)
                        <tr>
                            <td>{{ $messages->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2">
                                        @php
                                            $initials = strtoupper(substr($message->name, 0, 2));
                                            $stateColors = [
                                                'success',
                                                'danger',
                                                'warning',
                                                'info',
                                                'dark',
                                                'primary',
                                                'secondary',
                                            ];
                                            $stateColor = $stateColors[$message->id % count($stateColors)];
                                        @endphp

                                        <span
                                            class="avatar-initial rounded-circle bg-label-{{ $stateColor }}">{{ $initials }}</span>

                                    </div>
                                    <span>{{ $message->name }}</span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    @if ($message->email)
                                        <div><i class="ti ti-mail me-1"></i> {{ $message->email }}</div>
                                    @endif
                                    @if ($message->phone)
                                        <div><i class="ti ti-phone me-1"></i> {{ $message->phone }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $message->subject ?? 'N/A' }}</td>
                            <td>{{ Str::limit($message->message, 50) }}</td>
                            <td>{{ $message->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-50">
                                    <button
                                        class="btn btn-sm btn-icon edit-record btn-text-secondary rounded-pill waves-effect"
                                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasMessage"
                                        onclick="viewEditMessage({{ $message->id }})">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <a href="{{ route('admin.contact-us.show', $message->id) }}"
                                        class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($messages->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center">{{ __('No messages found') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer d-flex justify-content-center pt-3">
            {{ $messages->appends(['search' => $search, 'per_page' => $perPage, 'sort' => $sort, 'order' => $order])->links() }}
        </div>
    </div>
@endsection
