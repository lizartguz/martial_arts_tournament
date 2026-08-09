<?php

namespace App\Http\Controllers;

use App\Models\SenvaDataM;
use App\Models\StationM;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PublicStationFeedC extends Controller
{
    private const PUBLIC_STATION_ID = 1;
    private const PUBLIC_LIMIT = 100;
    private const PUBLIC_PER_PAGE = 20;
    private const HIDDEN_COLUMNS = [
        'station_id',
        'state',
        'created_at',
        'updated_at',
    ];

    /**
     * Muestra los ultimos registros publicos de la estacion interna.
     */
    public function index(Request $request): View
    {
        $this->logPublicAccess($request);

        $connection = $this->currentYearConnection();
        $stationName = StationM::query()
            ->whereKey(self::PUBLIC_STATION_ID)
            ->value('name');

        $columns = array_values(array_filter(
            Schema::connection($connection)->getColumnListing('senva_data'),
            fn ($column) => !in_array($column, self::HIDDEN_COLUMNS, true)
        ));

        $records = SenvaDataM::query()
            ->where('station_id', self::PUBLIC_STATION_ID)
            ->orderByDesc('receipt_date')
            ->limit(self::PUBLIC_LIMIT)
            ->get(array_merge(['station_id'], $columns));

        $page = LengthAwarePaginator::resolveCurrentPage();
        $offset = ($page - 1) * self::PUBLIC_PER_PAGE;
        $pageItems = $records->slice($offset, self::PUBLIC_PER_PAGE)->values();

        $paginatedRecords = new LengthAwarePaginator(
            $pageItems,
            $records->count(),
            self::PUBLIC_PER_PAGE,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('public.station-feed', [
            'columns' => $columns,
            'records' => $paginatedRecords,
            'limit' => self::PUBLIC_LIMIT,
            'perPage' => self::PUBLIC_PER_PAGE,
            'stationName' => $stationName,
        ]);
    }

    /**
     * Elimina los registros publicos de la estacion y registra la accion.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->currentYearConnection();

        $deletedRecords = SenvaDataM::query()
            ->where('station_id', self::PUBLIC_STATION_ID)
            ->delete();

        Log::warning('[PublicStationFeed] Records deleted', [
            'deleted_at' => now()->toDateTimeString(),
            'station_id' => self::PUBLIC_STATION_ID,
            'deleted_records' => $deletedRecords,
            'ip' => $request->ip(),
            'forwarded_for' => $request->header('x-forwarded-for'),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->headers->get('referer'),
            'accept_language' => $request->headers->get('accept-language'),
        ]);

        return redirect()
            ->route('public.station.feed')
            ->with('status', "Se eliminaron {$deletedRecords} registros correctamente.");
    }

    /**
     * Resuelve la conexion anual vigente para consultar datos publicos.
     */
    private function currentYearConnection(): string
    {
        $connection = 'senvatec_db_' . now()->year;

        abort_unless(
            config("database.connections.{$connection}"),
            503,
            'La base de datos del año actual no está disponible.'
        );

        return $connection;
    }

    /**
     * Registra informacion basica de cada acceso a la vista publica.
     */
    private function logPublicAccess(Request $request): void
    {
        Log::info('[PublicStationFeed] Access', [
            'accessed_at' => now()->toDateTimeString(),
            'station_id' => self::PUBLIC_STATION_ID,
            'ip' => $request->ip(),
            'forwarded_for' => $request->header('x-forwarded-for'),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'page' => $request->query('page'),
            'user_agent' => $request->userAgent(),
            'referer' => $request->headers->get('referer'),
            'accept_language' => $request->headers->get('accept-language'),
        ]);
    }
}
