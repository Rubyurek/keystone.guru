{{ Form::open(['route' => 'dungeonroute.temporary.savenew']) }}
<div class="container">
    @if( !isset($model) )
        @include('common.dungeon.select', ['id' => 'dungeon_id_select', 'showAll' => false])
    @endif

    <div class="form-group">
        <div class="text-info">
            @guest
                <i class="fas fa-info-circle"></i> {{ sprintf(
                    __('views/common.forms.createtemporaryroute.unregistered_user_message'),
                    config('keystoneguru.sandbox_dungeon_route_expires_hours')
                    )
                }}
            @else
                <i class="fas fa-info-circle"></i> {{
            sprintf(
                __('views/common.forms.createtemporaryroute.registered_user_message'),
                config('keystoneguru.sandbox_dungeon_route_expires_hours')
            )
                }}
            @endguest
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-group">
            {!! Form::submit(__('views/common.forms.createtemporaryroute.create_route'), ['class' => 'btn btn-info col-md-auto']) !!}
        </div>
    </div>
</div>

{!! Form::close() !!}