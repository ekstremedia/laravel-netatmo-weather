{{-- src/resources/views/memoryvehicle/partials/vehicles_table.blade.php --}}
<div class="min-w-full overflow-hidden overflow-x-auto align-middle shadow sm:rounded-lg">
    <table class="min-w-full">
        <thead>
        <tr>
            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                {{ trans('memoryapp::messages.vehicles') }}
            </th>
            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                Reg
            </th>
            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                Year
            </th>
            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
        </tr>
        </thead>
        <tbody class="bg-white">
        @foreach ($vehicles as $vehicle)
            <tr>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                    <a href="{{ route('memory.vehicles.show', $vehicle->uuid) }}"
                       class="text-blue-600 hover:text-blue-900">
                        @if($vehicle->brand)
                            {{ $vehicle->brand }}
                        @endif
                        @if($vehicle->model)
                            {{ $vehicle->model }}
                        @endif
                        @if($vehicle->name)
                            ({{ $vehicle->name }})
                        @endif
                    </a>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                    <a href="{{ route('memory.vehicles.show', $vehicle->uuid) }}"
                       class="text-blue-600 hover:text-blue-900">
                        {{ $vehicle->plate_number }}
                    </a>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                    <a href="{{ route('memory.vehicles.show', $vehicle->uuid) }}"
                       class="text-blue-600 hover:text-blue-900">
                        {{ $vehicle->year }}
                    </a>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-right text-sm leading-5 font-medium">
                    <a href="{{ route('memory.vehicles.fuel.create', $vehicle) }}"
                       class="text-indigo-600 hover:text-indigo-900">Fuel</a>
                    <a href="{{ route('memory.vehicles.edit', $vehicle) }}"
                       class="text-indigo-600 hover:text-indigo-900">Edit</a>
                    <form class="inline" action="{{ route('memory.vehicles.destroy', $vehicle) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 ml-4">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
