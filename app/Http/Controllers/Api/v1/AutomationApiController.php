<?php

namespace App\Http\Controllers\Api\v1;

use App\AI\Exceptions\AIProviderException;
use App\Automation\Exceptions\UnsupportedAutomationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\ExecuteAutomationRequest;
use App\Services\Automation\AutomationExecutor;
use Illuminate\Http\JsonResponse;

class AutomationApiController extends Controller
{
    public function execute(
        ExecuteAutomationRequest $request,
        AutomationExecutor $automationExecutor,
    ): JsonResponse {
        try {
            // El controlador se mantiene liviano: valida en el request y delega la logica al servicio.
            $result = $automationExecutor->execute($request->validated('text'));

            return response()->json([
                'success' => true,
                'message' => 'Automation executed successfully.',
                'data' => $result,
            ]);
        } catch (UnsupportedAutomationException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'data' => null,
            ], 422);
        } catch (AIProviderException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'data' => null,
            ], 502);
        }
    }
}
