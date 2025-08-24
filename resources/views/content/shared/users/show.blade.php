@extends('layouts/layoutMaster')

@section('title', __('User Profile'))

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-user-view.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('content')
    <div class="row">
        <!-- User Card -->
        <div class="col-xl-4 col-lg-5 col-md-5">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="user-avatar-section">
                        <div class="d-flex align-items-center flex-column">
                            <div class="avatar avatar-xl mb-3">
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
                                    <img src="{{ asset($user->image) }}" alt="User profile picture" class="rounded-circle">
                                @elseif($user->profile_photo_path)
                                    <img src="{{ asset($user->profile_photo_path) }}" alt="User profile picture"
                                        class="rounded-circle">
                                @else
                                    <span
                                        class="avatar-initial rounded-circle bg-label-{{ $stateColor }}">{{ $initials }}</span>
                                @endif
                            </div>
                            <div class="text-center mb-4">
                                <h4 class="mb-1">{{ $user->name }}</h4>
                                <span class="badge bg-label-{{ $stateColor }} mb-3">{{ ucfirst($user->type) }}</span>

                                @if ($user->email || $user->phone)
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        @if ($user->email)
                                            <a href="mailto:{{ $user->email }}"
                                                class="btn btn-icon btn-sm btn-label-secondary">
                                                <i class="ti ti-mail ti-sm"></i>
                                            </a>
                                        @endif
                                        @if ($user->phone)
                                            <a href="tel:{{ $user->phone }}"
                                                class="btn btn-icon btn-sm btn-label-secondary">
                                                <i class="ti ti-phone ti-sm"></i>
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <h5 class="pb-2 border-bottom mb-4">{{ __('Stats') }}</h5>
                    <div class="d-flex flex-column mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-xs">
                                    <div class="avatar-initial rounded bg-label-primary">
                                        <i class="ti ti-calendar ti-xs"></i>
                                    </div>
                                </div>
                                <h6 class="mb-0">{{ __('Jobs') }}</h6>
                            </div>
                            <h6 class="mb-0">{{ $user->jobApplications()->count() }}</h6>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-xs">
                                    <div class="avatar-initial rounded bg-label-success">
                                        <i class="ti ti-home ti-xs"></i>
                                    </div>
                                </div>
                                <h6 class="mb-0">{{ __('received Ratings') }}</h6>
                            </div>
                            <h6 class="mb-0">{{ $user->receivedRatings()->count() }}</h6>
                        </div>

                        {{-- <div class="d-flex justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-xs">
                                    <div class="avatar-initial rounded bg-label-info">
                                        <i class="ti ti-map-pin ti-xs"></i>
                                    </div>
                                </div>
                                <h6 class="mb-0">{{ __('Gathering Points') }}</h6>
                            </div>
                            <h6 class="mb-0">{{ 0 }}</h6>
                        </div> --}}
                    </div>

                    <div class="mt-4">
                        {{-- <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary w-100 mb-3">
                            <i class="ti ti-edit me-1"></i> {{ __('Edit User') }}
                        </a> --}}
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="ti ti-arrow-left me-1"></i> {{ __('Back to Users') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Content -->
        <div class="col-xl-8 col-lg-7 col-md-7">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">{{ __('User Information') }}</h5>
                    </div>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="userActions" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userActions">
                            <a class="dropdown-item" href="{{ route('admin.users.edit', $user->id) }}">
                                <i class="ti ti-edit me-1"></i> {{ __('Edit User') }}
                            </a>
                            <a class="dropdown-item text-danger delete-record" href="javascript:void(0);"
                                data-id="{{ $user->id }}">
                                <i class="ti ti-trash me-1"></i> {{ __('Delete User') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-bordered mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                                type="button" role="tab" aria-controls="profile" aria-selected="true">
                                <i class="ti ti-user me-1"></i> {{ __('Profile') }}
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="jobs-tab" data-bs-toggle="tab" data-bs-target="#jobs"
                                type="button" role="tab" aria-controls="jobs" aria-selected="false">
                                <i class="ti ti-briefcase me-1"></i> {{ __('Jobs') }}
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Profile Tab -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-lg-6">
                                    <h6 class="pb-2 border-bottom mb-4">{{ __('Basic Information') }}</h6>
                                    <div class="mb-4">
                                        <div class="d-flex align-items-start mb-3">
                                            <span class="text-muted fw-medium me-3"
                                                style="min-width: 100px;">{{ __('Full Name') }}</span>
                                            <span>{{ $user->name }}</span>
                                        </div>
                                        @if ($user->name_ar || $user->name_en)
                                            <div class="d-flex align-items-start mb-3">
                                                <span class="text-muted fw-medium me-3"
                                                    style="min-width: 100px;">{{ __('Name (AR/EN)') }}</span>
                                                <span>{{ $user->name_ar ?? 'N/A' }} / {{ $user->name_en ?? 'N/A' }}</span>
                                            </div>
                                        @endif
                                        <div class="d-flex align-items-start mb-3">
                                            <span class="text-muted fw-medium me-3"
                                                style="min-width: 100px;">{{ __('Email') }}</span>
                                            <span>{{ $user->email ?? __('N/A') }}</span>
                                        </div>
                                        <div class="d-flex align-items-start mb-3">
                                            <span class="text-muted fw-medium me-3"
                                                style="min-width: 100px;">{{ __('Phone') }}</span>
                                            <span>{{ $user->phone ?? __('N/A') }}</span>
                                        </div>
                                        <div class="d-flex align-items-start mb-3">
                                            <span class="text-muted fw-medium me-3"
                                                style="min-width: 100px;">{{ __('User ID') }}</span>
                                            <span>{{ $user->identity_id ?? __('N/A') }}</span>
                                        </div>
                                        <div class="d-flex align-items-start mb-3">
                                            <span class="text-muted fw-medium me-3"
                                                style="min-width: 100px;">{{ __('Gender') }}</span>
                                            <span>{{ ucfirst($user->gender ?? __('N/A')) }}</span>
                                        </div>
                                        <div class="d-flex align-items-start mb-3">
                                            <span class="text-muted fw-medium me-3"
                                                style="min-width: 100px;">{{ __('Age') }}</span>
                                            <span>{{ $user->age ?? __('N/A') }}</span>
                                        </div>
                                        <div class="d-flex align-items-start mb-3">
                                            <span class="text-muted fw-medium me-3"
                                                style="min-width: 100px;">{{ __('Type') }}</span>
                                            <span
                                                class="badge bg-label-{{ $stateColor }}">{{ ucfirst($user->type) }}</span>
                                        </div>
                                        <div class="d-flex align-items-start mb-3">
                                            <span class="text-muted fw-medium me-3"
                                                style="min-width: 100px;">{{ __('Status') }}</span>
                                            <span class="badge bg-label-{{ $user->status ? 'success' : 'danger' }}">
                                                {{ $user->status ? __('Active') : __('Inactive') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Information -->
                                <div class="col-lg-6">
                                    <h6 class="pb-2 border-bottom mb-4">{{ __('Additional Information') }}</h6>
                                    <div class="mb-4">
                                        <div class="d-flex align-items-start mb-3">
                                            <span class="text-muted fw-medium me-3"
                                                style="min-width: 100px;">{{ __('Nationality') }}</span>
                                            <span>{{ $user->country->name_ar ?? __('N/A') }}</span>
                                        </div>
                                        <div class="d-flex align-items-start mb-3">
                                            <span class="text-muted fw-medium me-3"
                                                style="min-width: 100px;">{{ __('City') }}</span>
                                            <span>{{ $user->city->name_ar ?? __('N/A') }}</span>
                                        </div>
                                        @if ($user->height_cm || $user->weight_kg)
                                            <div class="d-flex align-items-start mb-3">
                                                <span class="text-muted fw-medium me-3"
                                                    style="min-width: 100px;">{{ __('Height/Weight') }}</span>
                                                <span>
                                                    {{ $user->height_cm ? $user->height_cm . ' cm' : 'N/A' }} /
                                                    {{ $user->weight_kg ? $user->weight_kg . ' kg' : 'N/A' }}
                                                </span>
                                            </div>
                                        @endif
                                        @if ($user->role)
                                            <div class="d-flex align-items-start mb-3">
                                                <span class="text-muted fw-medium me-3"
                                                    style="min-width: 100px;">{{ __('Role') }}</span>
                                                <span>{{ $user->role }}</span>
                                            </div>
                                        @endif
                                        @if ($user->education_level)
                                            <div class="d-flex align-items-start mb-3">
                                                <span class="text-muted fw-medium me-3"
                                                    style="min-width: 100px;">{{ __('Education') }}</span>
                                                <span>{{ $user->education_level }}</span>
                                            </div>
                                        @endif
                                        @if ($user->language)
                                            <div class="d-flex align-items-start mb-3">
                                                <span class="text-muted fw-medium me-3"
                                                    style="min-width: 100px;">{{ __('Languages') }}</span>
                                                <span>
                                                    @if (is_array($user->language) || is_object($user->language))
                                                        @foreach ($user->language as $lang => $level)
                                                            <span class="badge bg-label-info mb-1">{{ $lang }}:
                                                                {{ $level }}</span>
                                                        @endforeach
                                                    @else
                                                        {{ __('N/A') }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                        <div class="d-flex align-items-start mb-3">
                                            <span class="text-muted fw-medium me-3"
                                                style="min-width: 100px;">{{ __('Health Status') }}</span>
                                            <span>
                                                @if ($user->suffers_disability !== null || $user->suffers_chronic_disease !== null)
                                                    @if ($user->suffers_disability)
                                                        <span
                                                            class="badge bg-label-warning me-1">{{ __('Disability') }}</span>
                                                    @endif
                                                    @if ($user->suffers_chronic_disease)
                                                        <span
                                                            class="badge bg-label-warning">{{ __('Chronic Disease') }}</span>
                                                    @endif
                                                    @if (!$user->suffers_disability && !$user->suffers_chronic_disease)
                                                        <span
                                                            class="badge bg-label-success">{{ __('No Health Issues') }}</span>
                                                    @endif
                                                @else
                                                    {{ __('N/A') }}
                                                @endif
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-start mb-3">
                                            <span class="text-muted fw-medium me-3"
                                                style="min-width: 100px;">{{ __('Joined') }}</span>
                                            <span>{{ $user->created_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Banking Information -->
                                @if ($user->bank_name || $user->iban || $user->card_holder_name || $user->stc_pay_number)
                                    <div class="col-12">
                                        <h6 class="pb-2 border-bottom mb-4">{{ __('Banking Information') }}</h6>
                                        <div class="row">
                                            @if ($user->bank_name)
                                                <div class="col-lg-6 mb-3">
                                                    <div class="d-flex align-items-start">
                                                        <span class="text-muted fw-medium me-3"
                                                            style="min-width: 100px;">{{ __('Bank Name') }}</span>
                                                        <span>{{ $user->bank_name }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($user->iban)
                                                <div class="col-lg-6 mb-3">
                                                    <div class="d-flex align-items-start">
                                                        <span class="text-muted fw-medium me-3"
                                                            style="min-width: 100px;">{{ __('IBAN') }}</span>
                                                        <span>{{ $user->iban }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($user->card_holder_name)
                                                <div class="col-lg-6 mb-3">
                                                    <div class="d-flex align-items-start">
                                                        <span class="text-muted fw-medium me-3"
                                                            style="min-width: 100px;">{{ __('Card Holder') }}</span>
                                                        <span>{{ $user->card_holder_name }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($user->stc_pay_number)
                                                <div class="col-lg-6 mb-3">
                                                    <div class="d-flex align-items-start">
                                                        <span class="text-muted fw-medium me-3"
                                                            style="min-width: 100px;">{{ __('STC Pay') }}</span>
                                                        <span>{{ $user->stc_pay_number }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Documents Section -->
                                @if ($user->certificate_path || $user->academic_qualification_path || $user->cv_path || $user->other_documents_path)
                                    <div class="col-12">
                                        <h6 class="pb-2 border-bottom mb-4">{{ __('Documents') }}</h6>
                                        <div class="d-flex flex-wrap gap-4">
                                            @if ($user->certificate_path)
                                                <div class="document-item">
                                                    <a href="{{ asset('storage/' . $user->certificate_path) }}"
                                                        target="_blank"
                                                        class="d-flex flex-column align-items-center p-3 border rounded text-center">
                                                        <i class="ti ti-file-certificate ti-lg mb-2"></i>
                                                        <span>{{ __('Certificate') }}</span>
                                                    </a>
                                                </div>
                                            @endif

                                            @if ($user->academic_qualification_path)
                                                <div class="document-item">
                                                    <a href="{{ asset('storage/' . $user->academic_qualification_path) }}"
                                                        target="_blank"
                                                        class="d-flex flex-column align-items-center p-3 border rounded text-center">
                                                        <i class="ti ti-certificate ti-lg mb-2"></i>
                                                        <span>{{ __('Qualification') }}</span>
                                                    </a>
                                                </div>
                                            @endif

                                            @if ($user->cv_path)
                                                <div class="document-item">
                                                    <a href="{{ asset('storage/' . $user->cv_path) }}" target="_blank"
                                                        class="d-flex flex-column align-items-center p-3 border rounded text-center">
                                                        <i class="ti ti-file-cv ti-lg mb-2"></i>
                                                        <span>{{ __('CV') }}</span>
                                                    </a>
                                                </div>
                                            @endif

                                            @if ($user->other_documents_path)
                                                <div class="document-item">
                                                    <a href="{{ asset('storage/' . $user->other_documents_path) }}"
                                                        target="_blank"
                                                        class="d-flex flex-column align-items-center p-3 border rounded text-center">
                                                        <i class="ti ti-files ti-lg mb-2"></i>
                                                        <span>{{ __('Other Docs') }}</span>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Events Tab -->
                        <div class="tab-pane fade" id="events" role="tabpanel" aria-labelledby="events-tab">
                            <h6 class="pb-2 border-bottom mb-4">{{ __('Event Participations') }}</h6>



                            @if (isset($user->createdEvents) && $user->createdEvents->isNotEmpty())
                                <h6 class="pb-2 border-bottom mb-4 mt-5">{{ __('Created Events') }}</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('Event Title') }}</th>
                                                <th>{{ __('Start Date') }}</th>
                                                <th>{{ __('End Date') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Location') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($user->createdEvents as $event)
                                                <tr>
                                                    <td>
                                                        @if (method_exists($event, 'getTranslation'))
                                                            {{ $event->getTranslation('title', app()->getLocale()) }}
                                                        @else
                                                            {{ $event->title ?? 'N/A' }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $event->start_date ? $event->start_date->format('Y-m-d H:i') : __('N/A') }}
                                                    </td>
                                                    <td>{{ $event->end_date ? $event->end_date->format('Y-m-d H:i') : __('N/A') }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $status = $event->status ?? 'unknown';
                                                            $statusClass =
                                                                [
                                                                    'upcoming' => 'info',
                                                                    'ongoing' => 'success',
                                                                    'completed' => 'primary',
                                                                    'cancelled' => 'danger',
                                                                    'unknown' => 'secondary',
                                                                ][$status] ?? 'secondary';
                                                        @endphp
                                                        <span class="badge bg-label-{{ $statusClass }}">
                                                            {{ ucfirst($status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $event->location ?? __('N/A') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>




                        <!-- Jobs Tab -->
                        <div class="tab-pane fade" id="jobs" role="tabpanel" aria-labelledby="jobs-tab">
                            <h6 class="pb-2 border-bottom mb-4">{{ __('Job Applications') }}</h6>

                            @if (isset($user->jobApplications) && $user->jobApplications->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('Position') }}</th>
                                                <th>{{ __('Event') }}</th>
                                                <th>{{ __('Applied At') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Contract') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($user->jobApplications as $application)
                                                <tr>
                                                    <td>
                                                        @if (isset($application->jobPosition))
                                                            @if (method_exists($application->jobPosition, 'getTranslation'))
                                                                {{ $application->jobPosition->position->getTranslation('title', app()->getLocale()) }}
                                                            @else
                                                                {{ $application->jobPosition->title ?? 'N/A' }}
                                                            @endif
                                                        @else
                                                            {{ __('N/A') }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (isset($application->jobPosition) && isset($application->jobPosition->event))
                                                            @if (method_exists($application->jobPosition->event, 'getTranslation'))
                                                                {{ $application->jobPosition->event->getTranslation('title', app()->getLocale()) }}
                                                            @else
                                                                {{ $application->jobPosition->event->title ?? 'N/A' }}
                                                            @endif
                                                        @else
                                                            {{ __('N/A') }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (isset($application->applied_at))
                                                            {{ $application->applied_at->format('Y-m-d H:i') }}
                                                        @else
                                                            {{ __('N/A') }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $status = $application->status ?? 'pending';
                                                            $statusClass =
                                                                [
                                                                    'pending' => 'warning',
                                                                    'under_review' => 'info',
                                                                    'approved' => 'success',
                                                                    'rejected' => 'danger',
                                                                    'cancelled' => 'secondary',
                                                                ][$status] ?? 'secondary';
                                                        @endphp
                                                        <span class="badge bg-label-{{ $statusClass }}">
                                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if (isset($application->contract_agreed) && $application->contract_agreed)
                                                            <span class="badge bg-label-success">
                                                                <i class="ti ti-check me-1"></i> {{ __('Agreed') }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-label-warning">
                                                                <i class="ti ti-clock me-1"></i> {{ __('Pending') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center p-4">
                                    <i class="ti ti-briefcase-off ti-3x mb-3 text-secondary"></i>
                                    <h6 class="mb-3">{{ __('No Job Applications') }}</h6>
                                    <p class="mb-0 text-muted">{{ __('This user hasn\'t applied for any jobs yet.') }}
                                    </p>
                                </div>
                            @endif

                            <!-- Buses for Drivers and Supervisors -->
                            @if (
                                ($user->type === 'driver' || $user->type === 'supervisor') &&
                                    (isset($user->driverBuses) || isset($user->supervisorBuses)))
                                <h6 class="pb-2 border-bottom mb-4 mt-5">{{ __('Assigned Buses') }}</h6>
                                @if ($user->type === 'driver' && isset($user->driverBuses) && $user->driverBuses->isNotEmpty())
                                    <h6 class="fw-medium mt-4 mb-3">{{ __('Buses as Driver') }}</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>{{ __('Bus Name') }}</th>
                                                    <th>{{ __('Bus Number') }}</th>
                                                    <th>{{ __('Seats') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($user->driverBuses as $bus)
                                                    <tr>
                                                        <td>
                                                            @if (method_exists($bus, 'getTranslation'))
                                                                {{ $bus->getTranslation('name', app()->getLocale()) }}
                                                            @else
                                                                {{ $bus->name ?? 'N/A' }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $bus->number ?? __('N/A') }}</td>
                                                        <td>{{ $bus->seats_count ?? __('N/A') }}</td>
                                                        <td>
                                                            @php
                                                                $status = $bus->status ?? 'active';
                                                                $statusClass =
                                                                    [
                                                                        'active' => 'success',
                                                                        'inactive' => 'danger',
                                                                        'maintenance' => 'warning',
                                                                    ][$status] ?? 'info';
                                                            @endphp
                                                            <span class="badge bg-label-{{ $statusClass }}">
                                                                {{ ucfirst($status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                @if ($user->type === 'supervisor' && isset($user->supervisorBuses) && $user->supervisorBuses->isNotEmpty())
                                    <h6 class="fw-medium mt-4 mb-3">{{ __('Buses as Supervisor') }}</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>{{ __('Bus Name') }}</th>
                                                    <th>{{ __('Bus Number') }}</th>
                                                    <th>{{ __('Seats') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($user->supervisorBuses as $bus)
                                                    <tr>
                                                        <td>
                                                            @if (method_exists($bus, 'getTranslation'))
                                                                {{ $bus->getTranslation('name', app()->getLocale()) }}
                                                            @else
                                                                {{ $bus->name ?? 'N/A' }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $bus->number ?? __('N/A') }}</td>
                                                        <td>{{ $bus->seats_count ?? __('N/A') }}</td>
                                                        <td>
                                                            @php
                                                                $status = $bus->status ?? 'active';
                                                                $statusClass =
                                                                    [
                                                                        'active' => 'success',
                                                                        'inactive' => 'danger',
                                                                        'maintenance' => 'warning',
                                                                    ][$status] ?? 'info';
                                                            @endphp
                                                            <span class="badge bg-label-{{ $statusClass }}">
                                                                {{ ucfirst($status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                @if (
                                    (!isset($user->driverBuses) || $user->driverBuses->isEmpty()) &&
                                        (!isset($user->supervisorBuses) || $user->supervisorBuses->isEmpty()))
                                    <div class="text-center p-4">
                                        <i class="ti ti-bus-off ti-3x mb-3 text-secondary"></i>
                                        <h6 class="mb-3">{{ __('No Assigned Buses') }}</h6>
                                        <p class="mb-0 text-muted">{{ __('This user has no assigned buses.') }}</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Delete User') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-4">
                        <i class="ti ti-alert-triangle text-danger ti-3x"></i>
                    </div>
                    <h4>{{ __('Are you sure?') }}</h4>
                    <p>{{ __('You won\'t be able to revert this!') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>
                    <form id="deleteUserForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('Yes, delete it!') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle delete confirmation
            const deleteButtons = document.querySelectorAll('.delete-record');
            const deleteUserForm = document.getElementById('deleteUserForm');
            const deleteConfirmationModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-id');
                    deleteUserForm.action = `{{ route('admin.users.index') }}/${userId}`;
                    deleteConfirmationModal.show();
                });
            });
        });
    </script>
@endsection
