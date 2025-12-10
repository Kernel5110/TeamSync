@extends('layouts.app')

@section('title', 'Logs de Auditoría - TeamSync')

@section('content')
<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px;">
    <h1 style="text-align: center; margin-bottom: 30px; color: #1f2937;">Logs de Auditoría</h1>

    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                <thead>
                    <tr style="background-color: #f9fafb; text-align: left;">
                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Usuario</th>
                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Acción</th>
                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Modelo</th>
                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Detalles</th>
                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">IP</th>
                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #1f2937; font-weight: 500;">
                                {{ $log->user ? $log->user->name : 'Sistema/Eliminado' }}
                                <br>
                                <span style="font-size: 0.75rem; color: #6b7280;">{{ $log->user ? $log->user->email : '' }}</span>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">
                                <span style="background-color: #e0e7ff; color: #4338ca; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                    {{ strtoupper($log->action) }}
                                </span>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #4b5563;">
                                {{ class_basename($log->model) }} #{{ $log->model_id }}
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #4b5563; font-size: 0.875rem;">
                                <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->details }}">
                                    {{ $log->details }}
                                </div>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 0.875rem;">
                                {{ $log->ip_address }}
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 0.875rem;">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 20px; text-align: center; color: #6b7280;">No hay registros de auditoría.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $logs->links('pagination::simple-tailwind') }}
        </div>
    </div>
</div>
@endsection
