@extends('layouts.admin.master')
@section('content')
    <div class="content-wrapper">
        @include('layouts.admin.content-header')
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-3"></div>
                    <div class="form-group col-6">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Edit Form</h3>
                            </div>
                            <form id="form"
                                action="{{ route('admin.password.update', Auth::guard('admin')->user()->id) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-12">
                                            <label class="form-label">Current Password</label>
                                            <input type="password" class="form-control" id="current_password"
                                                name="current_password" placeholder="Current Password" required>
                                            <span id="check-current-password"></span>
                                        </div>
                                        <div class="form-group col-12">
                                            <label class="form-label">New Password</label>
                                            <input type="password" class="form-control" id="new_password"
                                                name="new_password" placeholder="New Password" required>
                                        </div>
                                        <div class="form-group col-12">
                                            <label class="form-label">Confirm Password</label>
                                            <input type="password" class="form-control" id="confirm_password"
                                                name="confirm_password" placeholder="Confirm Password" required>
                                            <span id="check-confirm-password"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button id="submit" type="submit" disabled class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#current_password').keyup(async function() {
                var current_password = $(this).val();
                url = "{{ route('admin.password.check') }}";
                const res = await nsAjaxPost(url, {
                    current_password: current_password
                });
                if (current_password) {
                    if (res) {
                        $('#check-current-password').html(
                            '<font color="green">Correct Password</font>');
                        $('#submit').removeAttr('disabled');
                    } else {
                        $('#check-current-password').html(
                            '<font color="red">Incorrect Password</font>');
                        $('#submit').attr('disabled', true);
                    }
                } else {
                    $('#check-current-password').html('');
                    $('#submit').attr('disabled', true);
                }
            });
            $('#confirm_password').keyup(function() {
                var confirm_password = $(this).val();
                var new_password = $('#new_password').val();

                if (confirm_password == new_password) {
                    $('#check-confirm-password').html('<font color="green">Password Matched</font>');
                } else {
                    $('#check-confirm-password').html('<font color="red">Password Mismatch</font>');
                }
            });
            $('form').on('submit', async function(e) {
                e.preventDefault();
                var new_password = $('#new_password').val();
                var confirm_password = $('#confirm_password').val();
                if (confirm_password != new_password) return alert("Mismatch Password!");
                var url = $(this).attr('action');
                const res = await nsAjaxPost(url, {
                    new_password: new_password
                });
                if (res) {
                    Swal.fire({
                        position: "middle-center",
                        icon: "success",
                        title: "Password Updated Successfully!",
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#form')[0].reset();
                    $('#check-confirm-password').html('');
                    $('#check-current-password').html('');
                    $('#submit').attr('disabled', true);
                }
            });
        });
    </script>
@endsection