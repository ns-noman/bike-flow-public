<!-- Sweet Alert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- jQuery -->
<script src="{{ asset('public/admin-assets') }}/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('public/admin-assets') }}/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>  $.widget.bridge('uibutton', $.ui.button) </script>
<!-- Bootstrap 4 -->
<script src="{{ asset('public/admin-assets') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- daterangepicker -->
<script src="{{ asset('public/admin-assets') }}/plugins/moment/moment.min.js"></script>
<script src="{{ asset('public/admin-assets') }}/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('public/admin-assets') }}/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="{{ asset('public/admin-assets') }}/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('public/admin-assets') }}/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="{{ asset('public/admin-assets') }}/dist/js/adminlte.js"></script>

<!-- DataTables  & Plugins -->
<script src="{{ asset('public/admin-assets') }}/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{ asset('public/admin-assets') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('public/admin-assets') }}/plugins/toastr/toastr.min.js"></script>
<!-- Select2 -->
<script src="{{ asset('public/admin-assets') }}/plugins/select2/js/select2.full.min.js"></script>

<script>
    function message(data)
    {
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "3000",
            "extendedTimeOut": "1000",
            "showEasingCommand": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        
        if (data.success==true) {
            toastr.success(data.message, "Success!");
        } else {
            toastr.warning(data.message, "Warning!");
        }
    }
</script>


<script>
	toastr.options = {
		"closeButton": false,
		"debug": false,
		"newestOnTop": false,
		"progressBar": true,
		"positionClass": "toast-top-right",
		"preventDuplicates": false,
		"onclick": null,
		"showDuration": "300",
		"hideDuration": "1000",
		"timeOut": "3000",
		"extendedTimeOut": "1000",
		"showEasingCommand": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut"
	}
	@if(session('alert'))
		if("{{ session('alert')['messageType'] == 'success' }}"){
			toastr.success("{{ session('alert')['message'] }}", "Success!");
		}
		if("{{ session('alert')['messageType'] == 'warning' }}"){
			toastr.warning("{{ session('alert')['message'] }}", "Warning!");
		}
    @endif
</script>


<script>
    $(document).ready(function(){

        $("#example1").DataTable({
            "responsive": false,
            "lengthChange": true,
            "autoWidth": true
        });
        $('.select2').select2();
    });
 
</script>

<script>
    $(document).ready(function(){
        $('.add-new').click(function(e) {
            e.preventDefault();
            window.location.replace($(this).attr('add-new'));
        });
    });

    // $(document).ready(function(){
    //     $(document).on('click', '.delete button', function(e) {
    //         e.preventDefault();
    //         let form = $(this).closest('form');
    //         Swal.fire({
    //             title: "Are you sure?",
    //             text: "You won't be able to revert this!",
    //             icon: "warning",
    //             showCancelButton: true,
    //             confirmButtonColor: "#3085d6",
    //             cancelButtonColor: "#d33",
    //             confirmButtonText: "Yes, delete it!"
    //         }).then((result) => {
    //             if (result.isConfirmed){
    //                 form.submit();
    //             }
    //         });
    //     });
    // });





    function nsAjaxPost(url, data) {
        return new Promise((resolve, reject) => {
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: url,
                data: data,
                type: 'POST',
                dataType: 'JSON',
                success: function(res) {
                    // Check if the response is valid JSON
                    if (res && typeof res === 'object') {
                        resolve(res);
                    } else {
                        reject({ success: false, message: 'Invalid response format' });
                    }
                },
                error: function(xhr, status, error) {
                    // Handle errors more gracefully
                    let errorMessage = 'An unexpected error occurred. Please try again later.';
                    if (xhr.responseJSON) {
                        // Use the error message from the server if available
                        errorMessage = xhr.responseJSON.message || errorMessage;
                    }
                    reject({ success: false, message: errorMessage });
                }
            });
        });
    }

    function nsAjaxGet(url){
        return new Promise((resolve, reject) => {
        $.ajax({
                url: url,
                method: 'GET',
                dataType: 'JSON',
                success: function(res){
                    resolve(res);
                },
                error: function(err) {
                    reject(err);
                }
            });
        });
    }

    function dataTable(options){
        let defaultOptions = {processing: true,serverSide: true,type: 'GET',order: [],searchReturn: false};
        options = {...defaultOptions, ...options};
        var table = $('#dataTable').DataTable({
            processing: options.processing,
            serverSide: options.serverSide,
            ajax: {
                url: options.url,
                type: options.type,
                dataSrc: function(res) {
                    // console.log("Response data:", res);
                    return res.data;
                }
            },
            columns: options.columns,
            rowCallback: function(row, data, index) {
                var pageInfo = table.page.info();
                var serialNumber = pageInfo.start + index + 1;
                $('td:eq(0)', row).html(serialNumber);
            },
            order: options.order,
            search: {
                return: defaultOptions.searchReturn
            }
        });

    }
    function nsSetItem(key,value) {
        localStorage.setItem(key, JSON.stringify(value));
    }
    function nsGetItem(key) {
        return JSON.parse(localStorage.getItem(key));
    }
    function nsSetOption(params) {
        if (!params.hasOwnProperty('valueColumn')) params.valueColumn = 'id';
        let options = '';
        if(params.hasOwnProperty('selectedValue')){
            if(params.defaultText){
                options += `<option ${params.selectedValue==params.defaultValue?'selected':null} value="${params.defaultValue}">${params.defaultText}</option>`;
            }
            params.data.forEach(item => {
                options += `<option ${params.selectedValue==item[params.valueColumn]?'selected':null} value="${item[params.valueColumn]}">${item[params.displayColumn]}</option>`;
            });
        }else{
            options += `<option value="${params.defaultValue}">${params.defaultText}</option>`;
            params.data.forEach(item => {
                options += `<option value="${item[params.valueColumn]}">${item[params.displayColumn]}</option>`;
            });
        }
        if(params.selectElementId){
            $('#' + params.selectElementId).html(options);
        }else{
            return options;
        }
    }

    function formatNumber(number) {
        const formatted = parseFloat(number).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
        return '৳ ' + formatted;
    }

</script>

