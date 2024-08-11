{{-- src/resources/views/netatmo/partials/form.blade.php --}}
<form method="POST"
      action="{{ isset($weatherStation) ? route('netatmo.update', $weatherStation) : route('netatmo.store') }}">
    @csrf
    @if(isset($weatherStation))
        @method('PUT')
    @endif

    @foreach ($fields as $field)
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="{{ $field['name'] }}">
                        {{ trans("netatmoweather::messages.weatherstation." . $field['name']) }}
                        @if($field['required'])
                            <span class="text-red-700">*</span>
                        @endif
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error($field['name']) border-red-500 @enderror"
                        id="{{ $field['name'] }}"
                        type="{{ $field['type'] }}"
                        name="{{ $field['name'] }}"
                        value="{{ old($field['name'], isset($weatherStation) ? $weatherStation->{$field['name']} : null) }}"
                        placeholder="{{ trans("netatmoweather::messages.weatherstation." . $field['name']) }}"
                        {{ $field['required'] ? 'required' : '' }}>

                    @error($field['name'])
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
        </div>
    @endforeach
    @include('netatmoweather::main.partials.form.actions', [
        'buttonText' => isset($weatherStation) ? trans("netatmoweather::messages.general.Update") : trans("netatmoweather::messages.general.Save")
        ])
</form>
