@extends('layouts.app')

@section('title')
    Data Gajis
@endsection

@section('content')
    @can('gaji_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.gajis.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.gaji.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.gaji.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Gaji">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.gaji.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.gaji.fields.member') }}
                            </th>
                            <th>
                                {{ trans('cruds.gaji.fields.gaji') }}
                            </th>
                            <th>
                                {{ trans('cruds.gaji.fields.keterangan') }}
                            </th>
                            <th>
                                {{ trans('cruds.gaji.fields.bagian') }}
                            </th>
                            <th>
                                {{ trans('cruds.gaji.fields.level') }}
                            </th>
                            <th>
                                {{ trans('cruds.gaji.fields.transportasi') }}
                            </th>
                            <th>
                                {{ trans('cruds.gaji.fields.performance') }}
                            </th>
                            <th>
                                {{ trans('cruds.gaji.fields.lain_lain') }}
                            </th>
                            <th>
                                {{ trans('cruds.gaji.fields.jumlah_lain') }}
                            </th>
                            <th>
                                {{ trans('cruds.gaji.fields.created_at') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gajis as $key => $gaji)
                            <tr data-entry-id="{{ $gaji->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $gaji->id ?? '' }}
                                </td>
                                <td>
                                    {{ $gaji->member->nama_lengkap ?? '' }}
                                </td>
                                <td>
                                    {{ $gaji->gaji ?? '' }}
                                </td>
                                <td>
                                    {{ $gaji->keterangan ?? '' }}
                                </td>
                                <td>
                                    {{ $gaji->bagian->nama ?? '' }}
                                </td>
                                <td>
                                    {{ $gaji->level->nama ?? '' }}
                                </td>
                                <td>
                                    {{ $gaji->transportasi ?? '' }}
                                </td>
                                <td>
                                    {{ $gaji->performance ?? '' }}
                                </td>
                                <td>
                                    {{ $gaji->lain_lain ?? '' }}
                                </td>
                                <td>
                                    {{ $gaji->jumlah_lain ?? '' }}
                                </td>
                                <td>
                                    {{ $gaji->created_at ?? '' }}
                                </td>
                                <td>
                                    @can('gaji_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.gajis.show', $gaji->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('gaji_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.gajis.edit', $gaji->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('gaji_delete')
                                        <form action="{{ route('admin.gajis.destroy', $gaji->id) }}" method="POST"
                                            onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                            style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="submit" class="btn btn-xs btn-danger"
                                                value="{{ trans('global.delete') }}">
                                        </form>
                                    @endcan

                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('gaji_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.gajis.massDestroy') }}",
                    className: 'btn-danger',
                    action: function(e, dt, node, config) {
                        var ids = $.map(dt.rows({
                            selected: true
                        }).nodes(), function(entry) {
                            return $(entry).data('entry-id')
                        });

                        if (ids.length === 0) {
                            alert('{{ trans('global.datatables.zero_selected') }}')

                            return
                        }

                        if (confirm('{{ trans('global.areYouSure') }}')) {
                            $.ajax({
                                    headers: {
                                        'x-csrf-token': _token
                                    },
                                    method: 'POST',
                                    url: config.url,
                                    data: {
                                        ids: ids,
                                        _method: 'DELETE'
                                    }
                                })
                                .done(function() {
                                    location.reload()
                                })
                        }
                    }
                }
                dtButtons.push(deleteButton)
            @endcan

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 100,
            });
            let table = $('.datatable-Gaji:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
