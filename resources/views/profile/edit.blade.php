<?php
/** @var \App\User $user */
$user = Auth::getUser();
$isOAuth = $user->password === '';
$menuItems = [
    ['icon' => 'fa-user', 'text' => __('Profile'), 'target' => '#profile'],
    ['icon' => 'fab fa-patreon', 'text' => __('Patreon'), 'target' => '#patreon'],
];
// Optionally add this menu item
if (!$isOAuth) {
    $menuItems[] = ['icon' => 'fa-key', 'text' => __('Change password'), 'target' => '#change-password'];
}
$menuItems[] = ['icon' => 'fa-user-secret', 'text' => __('Privacy'), 'target' => '#privacy'];

$menuTitle = sprintf(__('%s\'s profile'), $user->name);
?>
@extends('layouts.app', ['wide' => true, 'title' => __('Profile'),
    'menuTitle' => $menuTitle, 'menuItems' => $menuItems,
    'model' => $user
])

@section('scripts')
    @parent

    <script type="text/javascript">
        $(function () {
            // Code for base app
            var appCode = _inlineManager.getInlineCode('layouts/app');
            appCode._newPassword('#new_password');
        });
    </script>
@endsection

@section('content')
    @include('common.general.modal', ['id' => 'team_select_modal'])

    <div class="tab-content">
        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            {{ Form::model($user, ['route' => ['profile.update', $user->name], 'method' => 'patch']) }}
            <h4>
                {{ $menuTitle }}
            </h4>
            @if($isOAuth && !$user->changed_username)
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name">
                        {{ __('Username') }}
                        <i class="fas fa-info-circle" data-toggle="tooltip"
                           title="{{ __('Since you logged in using an external Authentication service, you may change your username once.') }}"></i>
                    </label>
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    @include('common.forms.form-error', ['key' => 'name'])
                </div>
            @endif
            @if(!$isOAuth)
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    {!! Form::label('email', __('Email')) !!}
                    {!! Form::text('email', null, ['class' => 'form-control']) !!}
                    @include('common.forms.form-error', ['key' => 'email'])
                </div>
            @endif
            <div class="form-group{{ $errors->has('game_server_region_id') ? ' has-error' : '' }}">
                {!! Form::label('game_server_region_id', __('Region')) !!}
                {!! Form::select('game_server_region_id', array_merge(['-1' => __('Select region')], \App\Models\GameServerRegion::all()->pluck('name', 'id')->toArray()), null, ['class' => 'form-control']) !!}
                @include('common.forms.form-error', ['key' => 'game_server_region_id'])
            </div>
            <div class="form-group{{ $errors->has('timezone') ? ' has-error' : '' }}">
                @include('common.forms.timezoneselect', ['selected' => $user->timezone])
            </div>

            {!! Form::submit(__('Submit'), ['class' => 'btn btn-info']) !!}
            {!! Form::close() !!}

            <div class="mt-4">
                <h3>{{ __('My routes') }}</h3>

                @include('common.dungeonroute.table', ['view' => 'profile'])
            </div>
        </div>

        <div class="tab-pane fade" id="patreon" role="tabpanel" aria-labelledby="patreon-tab">
            <h4>
                {{ __('Patreon') }}
            </h4>
            @isset($user->patreondata)
                <a class="btn patreon-color text-white" href="{{ route('patreon.unlink') }}" target="_blank">
                    {{ __('Unlink from Patreon') }}
                </a>

                <p class="mt-2">
                    <span class="text-info"><i class="fa fa-check-circle"></i></span>
                    {{ __('Your account is linked to Patreon. Thank you!') }}
                </p>
            @else
                <a class="btn patreon-color text-white" href="{{
                        'https://patreon.com/oauth2/authorize?' . http_build_query(
                            ['response_type' => 'code',
                            'client_id' => env('PATREON_CLIENT_ID'),
                            'redirect_uri' => route('patreon.link'),
                            'state' => csrf_token()
                            ])
                        }}" target="_blank">{{ __('Link to Patreon') }}</a>

                <p class="mt-2">
                    <span class="text-info"><i class="fa fa-info-circle"></i></span>
                    {{ __('In order to claim your Patreon rewards, you need to link your Patreon account') }}
                </p>
            @endisset
            <p class="text-warning mt-2">
                <i class="fa fa-exclamation-triangle"></i>
                {{ __('Patreon implementation is experimental. If your rewards are not available after linking with your Patreon, please contact me directly on Discord or Patreon and I will fix it for you.') }}
            </p>
        </div>

        @if(!$isOAuth)
            <div class="tab-pane fade" id="change-password" role="tabpanel" aria-labelledby="change-password-tab">
                <h4>
                    {{ __('Change password') }}
                </h4>
                {{--$user->email is intended, since that is the actual username--}}
                {{ Form::model($user, ['route' => ['profile.changepassword', $user->name], 'method' => 'patch']) }}
                {!! Form::hidden('username', $user->email) !!}
                <div class="form-group{{ $errors->has('current_password') ? ' has-error' : '' }}">
                    {!! Form::label('current_password', __('Current password')) !!}
                    {!! Form::password('current_password', ['class' => 'form-control', 'autocomplete' => 'current-password']) !!}
                    @include('common.forms.form-error', ['key' => 'current_password'])
                </div>

                <div class="form-group{{ $errors->has('new_password') ? ' has-error' : '' }}">
                    {!! Form::label('new_password', __('New password')) !!}
                    {!! Form::password('new_password', ['id' => 'new_password', 'class' => 'form-control', 'autocomplete' => 'new-password']) !!}
                    @include('common.forms.form-error', ['key' => 'new_password'])
                </div>


                <div class="form-group{{ $errors->has('new_password-confirm') ? ' has-error' : '' }}">
                    {!! Form::label('new_password-confirm', __('New password (confirm)')) !!}
                    {!! Form::password('new_password-confirm', ['class' => 'form-control', 'autocomplete' => 'new-password']) !!}
                    @include('common.forms.form-error', ['key' => 'new_password-confirm'])
                </div>

                {!! Form::submit(__('Submit'), ['class' => 'btn btn-info']) !!}

                {!! Form::close() !!}
            </div>
        @endif

        <div class="tab-pane fade" id="privacy" role="tabpanel" aria-labelledby="privacy-tab">
            <h4>
                {{ __('Privacy') }}
            </h4>
            {{ Form::model($user, ['route' => ['profile.updateprivacy', $user->name], 'method' => 'patch']) }}
            <div class="form-group{{ $errors->has('analytics_cookie_opt_out') ? ' has-error' : '' }}">
                {!! Form::label('analytics_cookie_opt_out', __('Google Analytics cookies opt-out')) !!}
                {!! Form::checkbox('analytics_cookie_opt_out', 1, $user->analytics_cookie_opt_out, ['class' => 'form-control left_checkbox']) !!}
            </div>
            <div class="form-group{{ $errors->has('adsense_no_personalized_ads') ? ' has-error' : '' }}">
                {!! Form::label('adsense_no_personalized_ads', __('Google Adsense no personalized ads')) !!}
                {!! Form::checkbox('adsense_no_personalized_ads', 1, $user->adsense_no_personalized_ads, ['class' => 'form-control left_checkbox']) !!}
            </div>
            {!! Form::submit(__('Submit'), ['class' => 'btn btn-info']) !!}
            {!! Form::close() !!}
        </div>
    </div>
@endsection