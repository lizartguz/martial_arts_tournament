<x-guest-layout>
    <style>
        .public-station-page {
            width: 100%;
            max-width: 100vw;
            overflow-x: hidden;
        }

        .public-station-card {
            width: 100%;
            max-width: calc(100vw - 2rem);
            min-width: 0;
            overflow: hidden;
        }

        .public-station-table-scroll {
            display: block;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .public-station-table-scroll {
            scrollbar-width: auto;
            scrollbar-color: #16a34a #e5e7eb;
        }

        .public-station-table-scroll::-webkit-scrollbar {
            height: 16px;
        }

        .public-station-table-scroll::-webkit-scrollbar-track {
            background: #e5e7eb;
            border-radius: 8px;
        }

        .public-station-table-scroll::-webkit-scrollbar-thumb {
            background: #16a34a;
            border: 3px solid #e5e7eb;
            border-radius: 8px;
        }

        .public-station-table-scroll::-webkit-scrollbar-thumb:hover {
            background: #15803d;
        }

        .public-station-table {
            width: max-content;
            min-width: 100%;
        }

        .public-station-modal[hidden] {
            display: none;
        }
    </style>

    <div class="public-station-page min-h-screen py-8 px-4">
        <div class="public-station-card mx-auto bg-white/95 shadow-xl rounded-2xl border border-gray-200">
            <div class="min-w-0">
                <div class="flex items-center justify-between gap-4 px-6 py-5 border-b border-gray-200 bg-green-600 text-white">
                    <h1 class="text-2xl font-semibold">Historial publico de registros meteorologicos</h1>
                    <button
                        type="button"
                        id="open-delete-records-modal"
                        class="shrink-0 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-green-600"
                    >
                        Eliminar registros
                    </button>
                </div>

                <div class="px-6 py-3 bg-green-700 border-b border-green-800 text-sm font-semibold text-white">
                    {{ $stationName }}
                </div>

                @if (session('status'))
                    <div class="border-b border-green-200 bg-green-50 px-6 py-3 text-sm font-medium text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="public-station-table-scroll pb-3">
                    <table class="public-station-table text-sm divide-y divide-gray-200 whitespace-nowrap">
                        <thead class="bg-gray-100">
                            <tr class="hover:bg-gray-50">
                                @foreach ($columns as $column)
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 border-r border-gray-200">
                                        {{ $column }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($records as $record)
                                <tr class="hover:bg-gray-50">
                                    @foreach ($columns as $column)
                                        <td class="px-4 py-3 align-top text-gray-800 border-r border-gray-100">
                                            @php($value = $record->{$column})
                                            {{ $value !== null && $value !== '' ? $value : '-' }}
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($columns) }}" class="px-4 py-6 text-center text-gray-500">
                                        No se encontraron registros disponibles para mostrar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($records->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-white">
                        {{ $records->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div
        id="delete-records-modal"
        class="public-station-modal fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="delete-records-title"
        hidden
    >
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-2xl">
            <h2 id="delete-records-title" class="text-xl font-semibold text-gray-900">
                Confirmar eliminación
            </h2>
            <p class="mt-3 text-sm leading-6 text-gray-600">
                Se eliminarán permanentemente todos los registros de {{ $stationName }} correspondientes al año actual. Esta acción no se puede deshacer.
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    id="close-delete-records-modal"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                >
                    Cancelar
                </button>

                <form method="POST" action="{{ route('public.station.feed.destroy') }}">
                    @csrf
                    <button
                        type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                    >
                        Sí, eliminar todo
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('delete-records-modal');
            const openButton = document.getElementById('open-delete-records-modal');
            const closeButton = document.getElementById('close-delete-records-modal');

            function closeModal() {
                modal.hidden = true;
                document.body.style.overflow = '';
                openButton.focus();
            }

            openButton.addEventListener('click', function () {
                modal.hidden = false;
                document.body.style.overflow = 'hidden';
                closeButton.focus();
            });

            closeButton.addEventListener('click', closeModal);

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !modal.hidden) {
                    closeModal();
                }
            });
        });
    </script>
</x-guest-layout>
