@extends('layouts.app')

@section('title')
    Bagian List
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Bagian</h5>
                    </div>
                    <x-crud-create permission="bagian_create" :url="route('bagian.create')" label="Add bagian" />
                </div>
            </div>
            <div class="card-body">
                @include('layouts.includes.messages')
                <div class="table-responsive">
                    <table class="table table-striped" id="myTable">
                        <thead>
                            <tr>
                                <th scope="col">nama</th>
                                <th scope="col">grade</th>
                                @canany(['bagian_edit', 'bagian_delete'])
                                    <th scope="col">action</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bagians as $bagian)
                                <tr>
                                    <td>{{ $bagian->nama }}</td>
                                    <td>{{ $bagian->grade }}</td>
                                    @canany(['bagian_edit', 'bagian_delete'])
                                        <td>
                                            <x-crud-actions
                                                class="d-flex"
                                                edit="bagian_edit"
                                                :edit-url="route('bagian.edit', $bagian->id)"
                                            />
                                        </td>
                                    @endcanany
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
