@php
    use App\Enums\TypeUserEnum;
    use Illuminate\Support\Str;

    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Edit User')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
@endsection

@section('page-script')
    <script>
        // Initialize phone mask
        const phoneMaskList = document.querySelectorAll('.phone-mask');
        if (phoneMaskList) {
            phoneMaskList.forEach(function(phoneMask) {
                new Cleave(phoneMask, {
                    phone: true,
                    phoneRegionCode: 'US'
                });
            });
        }

        // Initialize select2
        const select2 = $('.select2');
        if (select2.length) {
            select2.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Select an option',
                    dropdownParent: $this.parent()
                });
            });
        }
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Edit User') }}</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">{{ __('Back to List') }}</a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $user->id }}">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="name">{{ __('Full Name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="email">{{ __('Email') }}</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $user->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="phone">{{ __('Phone') }}</label>
                                    <input type="text"
                                        class="form-control phone-mask @error('phone') is-invalid @enderror" id="phone"
                                        name="phone" value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="identity_id">{{ __('Identity ID') }}</label>
                                    <input type="text" class="form-control @error('identity_id') is-invalid @enderror"
                                        id="identity_id" name="identity_id"
                                        value="{{ old('identity_id', $user->identity_id) }}">
                                    @error('identity_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="password">{{ __('Password') }}</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password"
                                            placeholder="{{ __('Leave blank to keep current password') }}">
                                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                    </div>
                                    <small class="text-muted">{{ __('Leave blank to keep current password') }}</small>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="type">{{ __('User Type') }} <span
                                            class="text-danger">*</span></label>
                                    <select id="type" class="form-select select2 @error('type') is-invalid @enderror"
                                        name="type" required>
                                        @foreach (TypeUserEnum::cases() as $type)
                                            <option value="{{ $type->value }}"
                                                {{ old('type', $user->type) == $type->value ? 'selected' : '' }}>
                                                {{ Str::headline($type->value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="age">{{ __('Age') }}</label>
                                    <input type="number" class="form-control @error('age') is-invalid @enderror"
                                        id="age" name="age" value="{{ old('age', $user->age) }}">
                                    @error('age')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="gender">{{ __('Gender') }}</label>
                                    <select id="gender" class="form-select select2 @error('gender') is-invalid @enderror"
                                        name="gender">
                                        <option value="">{{ __('Select Gender') }}</option>
                                        <option value="male"
                                            {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>
                                            {{ __('Male') }}</option>
                                        <option value="female"
                                            {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>
                                            {{ __('Female') }}</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="image">{{ __('Profile Image') }}</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror"
                                        id="image" name="image">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    @if ($user->image)
                                        <div class="mt-2">
                                            <img src="{{ Storage::url($user->image) }}" alt="{{ $user->name }}"
                                                class="rounded" style="max-height: 100px">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3 mt-4">
                                    <input class="form-check-input" type="checkbox" id="status" name="status"
                                        {{ $user->status ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">{{ __('Active Status') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ __('Update') }}</button>
                                <a href="{{ route('admin.users.index') }}"
                                    class="btn btn-label-secondary">{{ __('Cancel') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


<!-- Import Users Modal -->
<div class="modal fade" id="rideTypeModal" tabindex="-1" aria-labelledby="rideTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rideTypeForm" method="POST" action="{{ route('admin.users.import') }}"
                enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="modal-header">
                    <h5 class="modal-title" id="rideTypeModalLabel">{{ __('Upload Users Excel') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modal_reason" class="form-label">{{ __('upload file here') }}</label>
                        <input type="file" class="form-control" id="modal_reason" name="file"
                            placeholder="{{ __('Enter a file') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<button type="button" class="btn btn-sm btn-info me-2" data-bs-toggle="modal" data-bs-target="#rideTypeModal">
    {{ __('Upload Users Excel') }}
</button>
