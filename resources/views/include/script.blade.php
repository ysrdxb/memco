<script src="{{ asset('public/all.js') }}"></script>
<script src="{{ asset('public/dist/js/theme.js') }}"></script>
<script src="{{ asset('public/js/sweetalert.js') }}"></script>
<script src="{{ asset('public/plugins/jquery-toast-plugin/dist/jquery.toast.min.js') }}"></script>

<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>

    Pusher.logToConsole = true;

    var pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}'
    });

    var channel = pusher.subscribe('user-{{ auth()->id() }}-channel');
    channel.bind('timetable-updated', function(data) {
        document.getElementById('notificationMessage').innerHTML = data.message;
        $('#notificationModal').modal('show');

            var html = '<div class="loader"></div>';
            $('#allCounters').html(html);                
            $.ajax({
                url: "{{ route('dashboard.data') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#allCounters').html(response.html);
                },
                error: function(xhr, status, error) {
                    // Handle error
                }
            });
              
    });
</script>  

<script>
function goBackOrHome() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = "/";
    }
}    
 
    
    
const APP_URL = '{{url('/')}}';
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function handleFormSubmit(formId, url, method, successCallback, errorCallback) {
    $(document).on('submit', formId, function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        html: response.message,
                    });
                    if (response.redirect && response.redirect !== '') {
                        window.location.href = response.redirect;
                    }
                } else if (response.status === 'error') {
                    console.log(response.message); // Log the error message to the console
                    swal.fire({
                        title: 'Error',
                        text: response.message.join('<br>'), // Display the error messages as a single string
                        icon: 'error',
                    });
                } else {
                    // Error message
                    swal.fire({
                        title: 'Error',
                        text: "Invalid response",
                        icon: 'error',
                    });
                }
                if (typeof successCallback === 'function') {
                    successCallback(response);
                }
            },
            error: function(xhr, status, error) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = Object.values(errors).flat().join('<br>');
                    Swal.fire({
                        icon: 'error',
                        title: 'Sorry!!',
                        html: errorMessage,
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Sorry!!',
                        html: 'An Error Occured',
                    });
                }
                if (typeof errorCallback === 'function') {
                    errorCallback(xhr.responseText);
                }
            }
        });
    });
}


function handleDeleteAction(deleteSelector, url, successCallback, errorCallback) {
  $(document).on('click', deleteSelector, function(event) {
    event.preventDefault();
    var itemId = $(this).attr('item-id');  

    Swal.fire({
      title: 'Are you sure?',
      text: 'You will not be able to recover this data!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'No, keep it'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "{{url('/')}}" + url + '/' + itemId,
          type: 'GET',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response) {
            if(response.status === 'success') {
                Swal.fire('Deleted!', response.message, response.status);
                window.location.href = response.redirect;
                if (typeof successCallback === 'function') {
                  successCallback(response);
                }
            } else {
                Swal.fire('Sorry!', response.message, 'error');
            }
          },
          error: function(xhr, status, response) {
            Swal.fire('Error!', response.message, response.status);
            if (typeof errorCallback === 'function') {
              errorCallback(xhr.responseText);
            }
          }
        });
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        //Swal.fire('Cancelled', 'Your data is safe :)', 'error');
      }
    });
  });
}
$(document).ready(function() {
    $.ajax({
        url: "{{ url('getCurrentUser') }}",
        method: 'GET',
        success: function(response) {
            setTimeout(function() {
                $('#current_user_locator').html(response);
            }, 0);            
        },
        error: function(xhr, status, error) {
           // console.error('');
        }
    });
});
</script>
@stack('script')