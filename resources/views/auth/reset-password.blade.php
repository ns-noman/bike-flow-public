
@php
    $basicInfo = App\Models\BasicInfo::first();
@endphp
@extends('layouts.frontend.master')
@section('content')
<!-- main-area -->
<main>
    <!-- breadcrumb-area -->
    @include('frontend.users.user-bread')
    <!-- breadcrumb-area-end -->

    <!-- my-account-area -->
    <section class="my-account-area pattern-bg pt-100 pb-100" data-background="{{ asset('public/frontend-assets/img/bg') }}/pattern_bg.jpg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10">
                    <div class="login-page-title">
                        <h2 class="title"><span>Reset Your Password</span></h2>
                    </div>
                    <div class="my-account-bg" data-background="{{ asset('public/frontend-assets/img/bg') }}/my_account_bg.png">
                        <div class="my-account-content">
                            <form method="POST" action="{{ route('password.store') }}" class="login-form">
                                @csrf
                                 <!-- Password Reset Token -->
                                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                <div class="form-grp">
                                    <label for="email">{{ __('translate.email') }} <span>*</span></label>
                                    <input type="text" id="email" name="email" value="{{ old('email', $request->email) }}">
                                    <span class="text-red"><x-input-error :messages="$errors->get('email')" class="mt-2" /></span>
                                </div>
                                <div class="form-grp">
                                    <label for="password">{{ __('translate.password') }} <span>*</span></label>
                                    <input type="password" id="password" name="password">
                                    <span><x-input-error :messages="$errors->get('password')" class="mt-2" /></span>
                                </div>
                                <div class="form-grp">
                                    <label for="password">CONFIRM PASSWORD<span>*</span></label>
                                    <input type="password"  type="password" name="password_confirmation" required>
                                    <span><x-input-error :messages="$errors->get('password')" class="mt-2" /></span>
                                </div>
                                <div class="form-grp-btn">
                                    <button type="submit" class="btn">{{ __('Reset Password') }}</button>
                                    <a href="" class="btn" hidden></a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- my-account-area-end -->
</main>
<!-- main-area-end -->
@endsection


{{-- <x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

       

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}







