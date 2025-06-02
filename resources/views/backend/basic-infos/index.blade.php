@extends('layouts.admin.master')
@section('content')
    <div class="content-wrapper">
        @include('layouts.admin.content-header')
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    @if($data['basicInfo'])
                    <section class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="bootstrap-data-table-panel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <tbody>
                                                <tr>
                                                    <th>Title</th>
                                                    <td>{{ $data['basicInfo']->title }}</td>
                                                </tr>
                                                <tr hidden>
                                                    <th>Meta Keywords</th>
                                                    <td>{{ $data['basicInfo']->meta_keywords }}</td>
                                                </tr>
                                                <tr hidden>
                                                    <th>Meta Description</th>
                                                    <td>{{ $data['basicInfo']->meta_description }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Phone</th>
                                                    <td>{{ $data['basicInfo']->phone }}</td>
                                                </tr>
                                                <tr hidden>
                                                    <th>Telephone</th>
                                                    <td>{{ $data['basicInfo']->telephone }}</td>
                                                </tr>
                                                <tr hidden>
                                                    <th>Fax</th>
                                                    <td>{{ $data['basicInfo']->fax }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Email</th>
                                                    <td>{{ $data['basicInfo']->email }}</td>
                                                </tr>
                                                <tr hidden>
                                                    <th>Location</th>
                                                    <td>{{ $data['basicInfo']->location }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Address</th>
                                                    <td>{{ $data['basicInfo']->address }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Logo</th>
                                                    <td>
                                                        @if ($data['basicInfo']->logo)
                                                            <img src="{{ asset('public/uploads/basic-info/' . $data['basicInfo']->logo) }}" height="121px" width="181px">
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Fav Icon</th>
                                                    <td>
                                                        @if ($data['basicInfo']->favicon)
                                                            <img src="{{ asset('public/uploads/basic-info/' . $data['basicInfo']->favicon) }}" height="32" width="32px">
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr hidden>
                                                    <th>Web Link</th>
                                                    <td>{{ $data['basicInfo']->web_link }}</td>
                                                </tr>
                                                <tr hidden>
                                                    <th>Facebook Link</th>
                                                    <td>{{ $data['basicInfo']->facebook_link }}</td>
                                                </tr>
                                                <tr hidden>
                                                    <th>Twitter Link</th>
                                                    <td>{{ $data['basicInfo']->twitter_link }}</td>
                                                </tr>
                                                <tr hidden>
                                                    <th>Linkedin Link</th>
                                                    <td>{{ $data['basicInfo']->linkedin_link }}</td>
                                                </tr>
                                                <tr hidden>
                                                    <th>Youtube Link</th>
                                                    <td>{{ $data['basicInfo']->youtube_link }}</td>
                                                </tr>

                                                <tr hidden>
                                                    <th>Assets value</th>
                                                    <td>{{ $data['basicInfo']->assets_value }}</td>
                                                </tr>
                                                <tr hidden>
                                                    <th>Total employees</th>
                                                    <td>{{ $data['basicInfo']->total_employees }}</td>
                                                </tr>
                                                <tr hidden>
                                                    <th>Total companies</th>
                                                    <td>{{ $data['basicInfo']->total_companies }}</td>
                                                </tr>
                                                <tr hidden>
                                                    <th>Start year</th>
                                                    <td>{{ $data['basicInfo']->start_year }}</td>
                                                </tr>
                                                <tr hidden>
                                                    <th>Map embed</th>
                                                    <td>{{ $data['basicInfo']->map_embed }}</td>
                                                </tr>
                                                <tr hidden>
                                                    <th>Video One</th>
                                                    <td>{{ $data['basicInfo']->video_embed_1 }}</td>
                                                </tr>
                                                <tr hidden>
                                                    <th>Video Two</th>
                                                    <td>{{ $data['basicInfo']->video_embed_2 }}</td>
                                                </tr>
                                                <tr hidden>
                                                    <th>Video Three</th>
                                                    <td>{{ $data['basicInfo']->video_embed_3 }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('basic-infos.edit', $data['basicInfo']->id) }}"class="btn btn-primary">Edit</a>
                            </div>
                        </div>
                    </section>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection